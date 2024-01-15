<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use \Firebase\JWT\JWT;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once'/var/www/html/fyntune-creditor-portal/vendor/autoload.php';
require_once'/var/www/html/fyntune-creditor-portal/vendor/phpmailer/phpmailer/src/Exception.php';
require_once'/var/www/html/fyntune-creditor-portal/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once'/var/www/html/fyntune-creditor-portal/vendor/phpmailer/phpmailer/src/SMTP.php';
// session_start(); //we need to call PHP's session object to access it through CI
class Customer_api extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('customerapimodel', '', TRUE);
        // Load these helper to create JWT tokens
        $this->load->helper(['core_helper', 'jwt', 'authorization_helper']);
        $this->load->model('api2/apimodel', '', TRUE);
        //$this->load->helper(['jwt', 'authorization']);

        ini_set('memory_limit', '25M');
        ini_set('upload_max_filesize', '25M');
        ini_set('post_max_size', '25M');
        ini_set('max_input_time', 3600);
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '-1');
        allowCrossOrgin();

    }

    public function proposal_no_checker()
    {

        $proposal_no = 'P-' . hexdec(uniqid());

        $proposal_id = $this->db->select('*')
            ->from('proposal_policy')
            ->where('proposal_no', $proposal_no)
            ->limit(1)
            ->get()
            ->row_array();
        if ($proposal_id > 0) {

            $this->proposal_no_checker();
        }
        return $proposal_no;
    }

    public function generateQuoteabc()
    {
        $plan_id = $this->input->post('plan_id');
        $premium = $this->input->post('premium');
        $si_type = $this->input->post('si_type_id');
        $cover = $this->input->post('cover');
        $policy_id = encrypt_decrypt_password($this->input->post('policy_id'), 'D');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $trace_id = encrypt_decrypt_password($this->input->post('trace_id'), 'D');
        $data['get_suminsured_type'] = $this->db->query("select suminsured_type from master_suminsured_type where suminsured_type_id = '$si_type'")->row_array();
        $data['member_ages'] = $this->db->select('fc.is_adult,fc.member_type, ma.member_age, ma.si_type_id,mst.suminsured_type')
            ->from('member_ages ma')
            ->join('master_suminsured_type mst', 'mst.suminsured_type_id = ma.si_type_id')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            ->get()
            ->result_array();

        $arr = ['adult_count' => 0, 'child_count' => 0];

        $min_age = 0;
        $max_age = 0;
        $arr_age = [];
        foreach ($data['member_ages'] as $key => $value) {

            if (!isset($si_type)) {
                $si_type = $value['si_type_id'];
            }
            $is_adult = $value['is_adult'];

            if ($value['is_adult'] == 'Y')
                $arr['adult_count']++;
            if ($value['is_adult'] == 'Y') {
                array_push($arr_age, $value['member_age']);
            } else
                $arr['child_count']++;
            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            $arr['member_age'] = $value['member_age'];
            $arr['is_adult'] = $value['is_adult'];

        }
        $adult = $arr['adult_count'];
        $child = $arr['child_count'];
        $data['max_age'] = max($arr_age);
        $max_age = $data['max_age'];
        $sub_type = [];
        $data['get_family_construct_data_all'] = $this->get_family_construct_data_policy_wise($policy_id);
        // print_r($data);die;
        $plan_details = $this->apimodel->getProductDetailsAll_diff($plan_id);
        $fam_const_det = $this->db->query("select MAX(adult_count) as adult_count,MAX(child_count) as child_count,GROUP_CONCAT(distinct(mpf.member_type_id)) as member_type_id FROM master_policy mp INNER JOIN master_policy_family_construct mpf inner join family_construct as fc WHERE mpf.member_type_id = fc.id AND  mp.policy_id = mpf.master_policy_id and mp.plan_id = '$plan_id' GROUP BY mp.plan_id")->row_array();
        $data['get_family_construct_data']['child'] = $fam_const_det['child_count'];
        $get_member_type = explode(',',$fam_const_det['member_type_id']);
        $arr_fam_det = [];
        foreach ($get_member_type as $key =>$valp)
        {

            $sql_t = $this->db->query("select * from family_construct where id = '$valp'")->row_array();

            $arr_fam_det[$key]['id'] = $sql_t['id'];
            $arr_fam_det[$key]['member_type'] = $sql_t['member_type'];
            $arr_fam_det[$key]['is_adult'] = $sql_t['is_adult'];

        }
        $data['get_family_construct_data']['adult'] = $arr_fam_det;
        // echo"<pre>";
        //  print_r($plan_details);die;
        //    $sum_premium = 0;
        $basis_id=array();
        foreach ($plan_details as $key => $policy) {

            $policy_id = $policy['policy_id'];
            $policy_sub_type_id = $policy['policy_sub_type_id'];
            $cover=$this->db->query("select sum_insured from master_policy_premium where isactive=1 AND master_policy_id=".$policy_id." order by policy_premium_id asc limit 1")->row()->sum_insured;
            $self_data_premium = $this->getAllpremium_single($policy_id, $cover, $data['max_age'],$adult,$child,$policy_sub_type_id);
            $policyBasisId = $this->db->query("select si_premium_basis_id as basis_id from master_policy_premium_basis_mapping   where master_policy_id = '$policy_id' and isactive = 1")->row()->basis_id;
            $basis_id[$policy_id]=$policyBasisId;
            //  var_dump($self_data_premium);exit;
            if($self_data_premium == 0){
                $sum_premium = $sum_premium + $self_data_premium;
            }else{
                $sum_premium = $sum_premium + $self_data_premium['amount'];
            }


            $sub_type [] = $policy['policy_sub_type_name'];
            if($self_data_premium == 0){
                $plan_details[$key]['self']['premium'] =  $self_data_premium;
            }else{
                $plan_details[$key]['self']['premium'] = $self_data_premium['amount'];
            }

            $plan_details[$key]['self']['sub_type_name'] = $policy['policy_sub_type_name'];
            $plan_details[$key]['self']['cover'] = $cover;

            $get_policy_wise_suminsured = $this->getPolicywiseSumInsured($policy_id);

            $plan_details[$key]['sum_insured'] = $get_policy_wise_suminsured;
            $sel_policy_plan = $this->db->query("select * from policy_member_plan_details where lead_id = '$lead_id' and policy_id = '$policy_id'")->row_array();
            if (!empty($sel_policy_plan)) {
                $plan_details[$key]['already_avail'][] = 1;
            } else {
                $plan_details[$key]['already_avail'][] = 0;
            }


        }
        if (!empty($sub_type)) {
            if (count($sub_type) > 1) {

                $sub_type_name = implode('+', $sub_type);
            } else {
                $sub_type_name = $sub_type[0];
            }
        }

        $data['self_data']['sub_type_name'] = $sub_type_name;
        $data['self_data']['premium'] = $sum_premium;
        $arr_data = [];
        $data_arr = [];

        if (!empty($plan_details)) {
            foreach ($plan_details as $get_plans) {


                //var_dump($get_plans['basis_id']);

                $data_arr['policy_sub_type_name'] = $get_plans['policy_sub_type_name'];
                $data_arr['is_combo'] = $get_plans['is_combo'];
                $data_arr['is_optional'] = $get_plans['is_optional'];
                $data_arr['creditor_name'] = $get_plans['creditor_name'];
                $data_arr['creditor_logo'] = $get_plans['creditor_logo'];
                $data_arr['sumInsured'] = $get_plans['sum_insured'];
                $data_arr['policy_id'] = $get_plans['policy_id'];
                $data_arr['self'] = $get_plans['self'];
                $data_arr['already_avail'] = $get_plans['already_avail'];


                array_push($arr_data, $data_arr);


            }
        }

        $data['policy_details'] = $arr_data;
        $data['basis_details'] = $basis_id;
        if ($data) {

            $data['policy_data'] = $data;
            $data['status'] = 200;
            echo json_encode($data);
            exit;
        } else {

            $data['status'] = 400;
            $data['msg'] = 'No policies were found';
            echo json_encode($data);
            exit;
        }

    }

    function get_summary_details()
    {
        $policy_id = encrypt_decrypt_password($this->input->post('policy_id'), 'D');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $trace_id = encrypt_decrypt_password($this->input->post('trace_id'), 'D');
        $plan_id = $this->input->post('plan_id');

        $query['insured_member'] = $this->db->query("select pmpd.policy_id,mpst.policy_sub_type_name,pmpd.cover,pmpd.premium,pmpd.total_premium,TIMESTAMPDIFF(YEAR, mp.policy_start_date, mp.policy_end_date) as tenure from policy_member_plan_details as pmpd join master_policy as mp join master_policy_sub_type as mpst where pmpd.policy_id = mp.policy_id and mp.policy_sub_type_id =  mpst.policy_sub_type_id and  pmpd.lead_id = $lead_id")->result_array();
//        var_dump($query);
        /*echo $this->db->last_query();
        exit;*/
        $plan_name['creditor'] = $this->db->query("select mc.creaditor_name,mp.plan_name,mc.creditor_logo from master_plan as mp join master_ceditors as mc where mp.creditor_id = mc.creditor_id and mp.plan_id = '$plan_id' ")->row_array();
        $abc =   array_merge($query,$plan_name);

        echo json_encode($abc);
        exit;
    }

    function Create_quote_self()
    {
        $policy_id = encrypt_decrypt_password($this->input->post('policy_id'), 'D');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $trace_id = encrypt_decrypt_password($this->input->post('trace_id'), 'D');
        $sel_con = $this->input->post('sel_con');
        $quote_info = $this->input->post('quote_info');
        // var_dump($quote_info);
        //exit;
        $cover = $this->input->post('cover');
        $total_premium = $this->input->post('total_premium');
        $plan_id = $this->input->post('plan_id');
        $resNew=$this->db->query("select * from master_policy where plan_id=".$plan_id)->result();
        foreach ($resNew as $row){
            $qq=$this->db->query("select (si_premium_basis_id) from master_policy_premium_basis_mapping where master_policy_id = ".$row->policy_id)->row();
            $si_premium_basis_id=$qq->si_premium_basis_id;
            if($si_premium_basis_id != 5){
                $query_del = $this->db->query("delete from policy_member_plan_details where lead_id = '$lead_id' AND policy_id=".$row->policy_id);
            }
        }
        //   exit;
        // $query_del = $this->db->query("delete from Create_quote_member_singlelink where lead_id = '$lead_id'");
        //  $query_del_paln = $this->db->query("delete from policy_member_plan_details where lead_id = '$lead_id'");

        $query = $this->db->query("select * from Create_quote_member_singlelink where lead_id = '$lead_id'")->row_array();
        if ($this->db->affected_rows() > 0) {

        } else {
            $sel_conArr=explode("+",$sel_con);
            $data_arrNew=array();
            foreach ($sel_conArr as $r){
                $data_arr = array(
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'member_type' => $r,
                    'is_adult' => 'Y'

                );
                array_push($data_arrNew,$data_arr);
            }
            $result = $this->db->insert_batch('Create_quote_member_singlelink', $data_arrNew);
        }


        $query_plan = $this->db->query("select * from policy_member_plan_details where lead_id = '$lead_id'")->row_array();
        if (count($query_plan) > 0) {

        } else {
            $quote = explode(',', $quote_info);
            //echo $cover;
            $coverNew = explode(',', $cover);
            // var_dump($coverNew);
            $covT=array();
            foreach ($coverNew as $cov){
                $coverNew1 = explode('-', $cov);
                $covT[]=$coverNew1[1];
            }
//var_dump($covT);exit;
            foreach ($quote as $key=>$val) {
                $val_imp = explode('-', $val);


                $data = array(
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'plan_id' => $plan_id,
                    'cover' => $covT[$key],
                    'premium' => $val_imp[1],
                    'policy_id' => $val_imp[0],
                    'total_premium' => $total_premium
                );
                $result_arr = $this->db->insert('policy_member_plan_details', $data);
            }


        }
    }

    public function getAllpremium_single($policy_id = 0, $sum_insured = 0, $age = 0, $adult = 0, $child = 0,$policy_sub_type_id=0)
    {


        $sum = 0;
        $policy = $this->db->query("select si_premium_basis_id as basis_id,master_policy_id as policy_id from master_policy_premium_basis_mapping   where master_policy_id = '$policy_id' and isactive = 1")->row_array();
        //print_r($policy); exit;
        if ($policy['basis_id'] == 1) {
            //echo json_encode(111111);exit;
            if($sum_insured == NULL){
                $sum_insured=$this->db->query("select sum_insured from master_policy_premium where master_policy_id=".$policy['policy_id']." AND sum_insured is not NULL order by sum_insured asc limit 1")->row()->sum_insured;
            }
            $premium = $this->getpolicypremiumflat($policy['policy_id'], $sum_insured);

        } else if ($policy['basis_id'] == 2) {
            if($sum_insured == NULL){
                $sum_insured=$this->db->query("select sum_insured from master_policy_premium where master_policy_id=".$policy['policy_id']." AND sum_insured is not NULL order by sum_insured asc limit 1")->row()->sum_insured;
            }
            // echo json_encode(22222);exit;
            $premium = $this->getpolicypremiumfamilyconstruct($policy['policy_id'], $sum_insured, $adult, $child);
//            var_dump($premium);
//            echo $this->db->last_query();
//            exit();
        } else if ($policy['basis_id'] == 3) {
            if($sum_insured == NULL){
                $sum_insured=$this->db->query("select sum_insured from master_policy_premium where master_policy_id=".$policy['policy_id']." AND sum_insured is not NULL order by sum_insured asc limit 1")->row()->sum_insured;
            }
            //echo json_encode(33333);exit;
            $age = $max_age;

            $premium = $this->getpolicypremiumfamilyconstructage($policy['policy_id'], $policy['policy_id'], $sum_insured, $adult, $child, $age);


        } else if ($policy['basis_id'] == 4) {
            // echo json_encode(4444);exit;
            if($sum_insured == NULL){
                $sum_insured=$this->db->query("select sum_insured from master_policy_premium where master_policy_id=".$policy['policy_id']." AND sum_insured is not NULL order by sum_insured asc limit 1")->row()->sum_insured;
            }

            if ($arr['is_adult'] == 'Y' && $child == 0) {
                if (is_array($arr_age)) {
                    foreach ($arr_age as $member_age_cal) {

                        $age = $member_age_cal;
                        $abc = $this->getpolicypremiummemberage($policy['policy_id'], $sum_insured, $age);
                        if (!empty($abc)) {
                            foreach ($abc as $rate_data) {
                                $sum = $sum + $rate_data;

                            }
                        }
                    }
                } else {
                    $abc = $this->getpolicypremiummemberage($policy['policy_id'], $sum_insured, $age);
                    if (!empty($abc)) {
                        foreach ($abc as $rate_data) {
                            $sum = $sum + $rate_data;

                        }
                    }
                }
                $premium['amount'] = $sum;
            }


        }else if ($policy['basis_id'] == 5) {
            $this->age = $age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );


            $result = $this->apimodel->getPerMileWisePremium2(array_merge($arr, ['number_of_ci' => 0, 'age' => $age, "tenure" => $this->tenure]));
            //echo $this->db->last_query();
            //   var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }else if ($policy['basis_id'] == 6) {
            $this->age = $age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );


            $result = $this->apimodel->getPolicyFamilyDeductable(array_merge($arr, ['adult_count' => $adult, 'child_count' => $child, "deductable" => 200000]));
            //echo $this->db->last_query();
            //   var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }else if ($policy['basis_id'] ==7) {
            $this->age = $age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );


            $result = $this->apimodel->getPolicyPerDayTenurePremium(array_merge($arr, ['tenure' => $this->tenure]));
            //echo $this->db->last_query();
            //   var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }

        return $premium;


    }

    function getMemebrDetails_single()
    {
        //echo 123;
        $policy_id = encrypt_decrypt_password($this->input->post('policy_id'), 'D');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $trace_id = encrypt_decrypt_password($this->input->post('trace_id'), 'D');
        $policy_data = $this->input->post('policy_data');
        /*   var_dump($policy_data);
           exit;*/
        $family_con = $policy_data[0]['family_construct'];
        $child=0;
        $adult=0;
        foreach ($family_con as $item){
            if($item['is_adult']=='Y'){
                $adult++;
            }else{
                $child++;
            }
        }
        $arr = ['adult_count' => $adult, 'child_count' => $child];
        $member_type = [];

        $arr_policy = [];
        $arr_premium = [];
        $total_premium = 0;
        $arr_all = [];
        $sub_name_imp = '';
        $i = 2;
//var_dump($policy_data);exit;
        foreach ($policy_data as $key => $value) {
            $value['policy_id']= preg_replace('/[^A-Za-z0-9\-]/', '', $value['policy_id']);
            ++$key;
            if (strpos($value['policy_id'], ',') !== false) {

                $exp_policy = explode(',', $value['policy_id']);
                foreach ($exp_policy as $key1 => $val) {
                    ++$key1;
                    $policy_sub_type_id=$this->db->query("select policy_sub_type_id from master_policy where policy_id=".$val)->row()->policy_sub_type_id;
                    $sub_name = $this->customerapimodel->getPolicySubTypeName($val);
                    $premium = $this->getAllpremium_single($val, $value['sum_insured'], $value['max_age'], $arr['adult_count'], $arr['child_count'],$policy_sub_type_id);
                    //   var_dump($premium);exit;
                    $total_premium = $total_premium + $premium['amount'];
                    array_push($arr_all, $sub_name['policy_sub_type_name']);
                    $arr_premium[$key1 + $key]['premium'] = $premium['amount'];
                    $arr_premium[$key1 + $key]['sub_type_name'] = $sub_name['policy_sub_type_name'];
                    $arr_premium[$key1 + $key]['policy_id'] = $val;
                    $arr_premium[$key1 + $key]['sum_insured'] = $value['sum_insured'];
                    $arr_premium[$key1 + $key]['plan_id'] = $value['plan_id'];
                    $arr_premium[$key1 + $key]['plan_flag'] = $value['plan_flag'];

                    if ($premium == 0) {


                        array_push($arr_policy, $sub_name['policy_sub_type_name']);

                    }


                }


            } else {
                $policy_sub_type_id=$this->db->query("select policy_sub_type_id from master_policy where policy_id=".$value['policy_id'])->row()->policy_sub_type_id;
                $premium = $this->getAllpremium_single($value['policy_id'], $value['sum_insured'], $value['max_age'], $arr['adult_count'], $arr['child_count'],$policy_sub_type_id);
                //var_dump($premium);
                $sub_name = $this->customerapimodel->getPolicySubTypeName($value['policy_id']);

                if ($premium == 0) {


                    array_push($arr_policy, $sub_name['policy_sub_type_name']);

                } else {
                    $total_premium = $total_premium + $premium['amount'];
                    array_push($arr_all, $sub_name['policy_sub_type_name']);
                    $arr_premium[$key]['premium'] = $premium['amount'];
                    $arr_premium[$key]['sub_type_name'] = $sub_name['policy_sub_type_name'];
                    $arr_premium[$key]['policy_id'] = $value['policy_id'];
                    $arr_premium[$key]['sum_insured'] = $value['sum_insured'];
                    $arr_premium[$key]['plan_id'] = $value['plan_id'];
                    $arr_premium[$key]['plan_flag'] = $value['plan_flag'];

                }

            }


        }
//exit;
        /*var_dump($arr_premium);
        exit;*/
        if (!empty($arr_policy)) {
            if (count($arr_policy) > 1) {
                $sub_imp = implode('+', $arr_policy);
            } else {
                $sub_imp = $arr_policy[0];
            }
            $data['status'] = 400;
            $data['msg'] = 'No premium were found for selected construct for ' . $sub_imp;
            echo json_encode($data);
            exit;
        } else {
            $det = $this->db->query("delete from Create_quote_member_singlelink where lead_id  = '$lead_id'");

            foreach ($family_con as $fam) {


                $data_arr = array(
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'member_type' => $fam['family_construct'],
                    'is_adult' => $fam['is_adult']

                );

                $result = $this->db->insert('Create_quote_member_singlelink', $data_arr);

                array_push($member_type, $fam['family_construct']);
                $is_adult = $fam['is_adult'];

                if ($is_adult == 'Y')
                    $arr['adult_count']++;

                else
                    $arr['child_count']++;
            }

            $det_member = $this->db->query("delete from policy_member_plan_details where lead_id  = '$lead_id'");

            foreach ($arr_premium as $all_data) {
                $data_member_det = array(
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'plan_id' => $all_data['plan_id'],
                    'policy_id' => $all_data['policy_id'],
                    'cover' => $all_data['sum_insured'],
                    'premium' => $all_data['premium'],
                    'total_premium' => $total_premium,
                    'additional_plan_flag' => $all_data['plan_flag'],


                );
//var_dump($data_member_det);
                $result_det = $this->db->insert('policy_member_plan_details', $data_member_det);
            }
//            exit;
            if (!empty($arr_all)) {
                if (count($arr_all) > 1) {
                    $sub_name_imp = implode('+', $arr_all);
                } else {
                    $sub_name_imp = $arr_all[0];
                }
            }
            if (!empty($member_type)) {
                if (count($member_type) > 1) {
                    $member_type_det = implode('+', $member_type);
                } else {
                    $member_type_det = $member_type[0];
                }
            }


            //    array_values($arr_premium);
            $data['total_premium'] = $total_premium;
            $data['member_type'] = $member_type_det;
            $data['total_premium'] = $total_premium;
            $data['sub_name_imp'] = $sub_name_imp;

            $data['policy_det'] = array_values($arr_premium);
            $data['status'] = 200;
            $data['msg'] = 'success';
            echo json_encode($data);
            exit;

        }
    }

    function get_family_construct_data_policy_wise($policy_id)
    {
        $query = $this->db->query("SELECT a.*,b.member_min_age, b.member_min_age_days, b.member_max_age  FROM family_construct a 
        INNER JOIN master_policy_family_construct b 
        ON a.id = b.member_type_id
        WHERE b.master_policy_id = '$policy_id' AND a.isactive = 1 
        AND b.isactive = 1
        GROUP BY b.member_type_id
        ORDER BY a.id")->result_array();

        return $query;
    }

    public function create_proposal()
    {
        $plan_id = $this->input->post('plan_id');
        $partner_id = $this->input->post('partner_id');
        $premium = $this->input->post('premium');
        // $policy_id = encrypt_decrypt_password($this->input->post('policy_id'),'D');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $trace_id = encrypt_decrypt_password($this->input->post('trace_id'), 'D');
        // echo $plan_id .'--'.$premium.'--'.$policy_id.'--'.$lead_id.'--'.$customer_id.'--'.$trace_id;exit;
        $proposal_detail = $this->db->query("select * from proposal_details where plan_id = '$plan_id' ")->row_array();


        $policy_detail_data = $this->db->query("select GROUP_CONCAT(mpfc.member_type_id) as member_type_id,mpfc.master_policy_id,mp.policy_sub_type_id,pmpd.cover,pmpd.premium,pmpd.total_premium,pmpd.plan_id from policy_member_plan_details as pmpd ,master_policy_family_construct as mpfc,master_policy as mp where pmpd.policy_id = mpfc.master_policy_id and mp.policy_id = mpfc.master_policy_id and mpfc.isactive=1 and  pmpd.lead_id = '$lead_id' group by mpfc.master_policy_id")->result_array();

        //$policy_detail = $this->db->query("select policy_id,policy_sub_type_id,mp.creditor_id from master_policy as mp,proposal_details as pd where mp.plan_id = pd.plan_id and  mp.policy_id in('$policy_id') and lead_id = '$lead_id'")->result_array();
        // echo $this->db->last_query();exit;
        //  print_r($policy_detail_data);exit;
        $this->db->query("delete from proposal_policy where lead_id = '$lead_id'");

        $this->db->query("delete from proposal_policy_member where lead_id = '$lead_id'");
        $this->db->query("delete from proposal_payment_details where lead_id = '$lead_id'");
        $total_premium = 0;
        foreach ($policy_detail_data as $value) {
            $arr_explode = explode(',', $value['member_type_id']);
            if ($partner_id) {
                // echo 11;
                $response = $this->getAllpremium_det_single($lead_id, $customer_id, $value['master_policy_id'], $value['cover'],$value['policy_sub_type_id']);
                // var_dump($response);

            } else {
                $response = $this->getAllpremium($lead_id, $customer_id, $value['master_policy_id'], $value['cover']);
            }



            $total_premium = $total_premium + $response['amount'];
            $policy_id = $value['master_policy_id'];
            $policy_detail = $this->db->query("select mp.policy_id,mp.policy_sub_type_id,mp.creditor_id,pd.proposal_details_id from master_policy as mp,proposal_details as pd where mp.plan_id = pd.plan_id and  mp.policy_id in('$policy_id') and lead_id = '$lead_id'")->row_array();

            //  print_r($policy_detail);
            $proposal_no = $this->proposal_no_checker();
            $data = array(
                'proposal_details_id' => $policy_detail['proposal_details_id'],
                'lead_id' => $lead_id,
                'trace_id' => $trace_id,
                'master_policy_id' => $policy_id,
                'proposal_no' => $proposal_no,
                'premium_amount' => $response['amount'],
                'policy_sub_type_id' => $policy_detail['policy_sub_type_id']

            );

            $this->db->insert("proposal_policy", $data);
            //  print_r($this->db->last_query());
            $proposal_id = $this->db->insert_id();
            $newData = array(
                'creditor_id' => $policy_detail['creditor_id'],
                'lead_id' => $lead_id,
                'trace_id' => $trace_id,
                'premium' => $response['amount'],
                'payment_status' => 'Pending',
                'payment_mode' => '1',
                'proposal_status' => 'PaymentPending',
                'created_at' => date("Y-m-d H:i:s"),
                'remark' => 'Payment Initiate',
                'payment_date' => date("Y-m-d H:i:s"),
            );
            $this->db->insert("proposal_payment_details", $newData);
            // echo $this->db->last_query();exit;
            $member_det = $this->db->query("select * from proposal_policy_member_details where lead_id = '$lead_id'")->result_array();

            foreach ($member_det as $val) {
                if (in_array($val['relation_with_proposal'], $arr_explode)) {

                    $data_det = array(
                        'lead_id' => $lead_id,
                        'trace_id' => $trace_id,
                        'policy_id' => $policy_id,
                        'proposal_policy_id' => $proposal_id,
                        'premium' => $response['amount'],
                        'member_id' => $val['member_id'],
                        'premium_with_tax' => $response['amount'],
                        'created_by' => $customer_id,
                        'created_at' => date('Y-m-d H:i:s'),
                    );
                    $this->db->insert("proposal_policy_member", $data_det);
                    // print_r($this->db->last_query());exit;

                    $data_plan_mem_arr = array(
                        'premium' => $response['amount']

                    );
                    $this->db->where('policy_id', $value['master_policy_id']);
                    $this->db->where('lead_id', $lead_id);
                    $this->db->update('policy_member_plan_details', $data_plan_mem_arr);

                }
            }

        }
        //exit;
        $data_plan_total_arr = array(
            'total_premium' => $total_premium

        );

        $this->db->where('lead_id', $lead_id);
        $this->db->update('policy_member_plan_details', $data_plan_total_arr);
        echo 1;
    }

    function updateCustomerDetails()
    {
        $partner_id = $this->input->post('partner_id');

        $dob = date("Y-m-d", strtotime($_POST['proposer_dob']));
        //$diff = date_diff(date_create(date('d-m-Y', strtotime($dob))) , date_create(date("Y-m-d")));
        //$age = $diff->format('%y');
        $ages = $this->cal_age(date('Y-m-d', strtotime($dob)));
        $age = $ages['age'];
        $age_type = $ages['age_type'];
        $validation['status'] = 'success';
        if ($partner_id) {

        } else {
            if($_POST['is_proposer_insured']){
                $validation = $this->validate_member_age($age_type, $age, $_POST['policy_id'], 1);
            }else{
                if($age < 18){
                    $data['status'] = 'error';//$memberType
                    $data['message'] = "Proposar's age should be minimum 18 Years";
                    echo json_encode($data);
                    exit;
                }

            }

        }
        //print_r($validation);exit;
        if ($validation['status'] == 'error') {
            echo json_encode($validation);
            exit;
        }
        $data = $this->db->update(
            'master_customer',
            [
                'first_name' => $_POST['fname'],
                'last_name' => $_POST['lname'],
                'full_name' => $_POST['fname'] . ' ' . $_POST['lname'],
                'gender' => $_POST['gender'],
                'dob' => $dob,
                'email_id' => $_POST['email'],
                'mobile_no' => $_POST['mobile_no'],
                'address_line1' => $_POST['proposer_address'],
                'address_line2' => $_POST['proposer_address2'],
                'pincode' => $_POST['proposer_pincode'],
                'city' => $_POST['proposer_city'],
                'state' => $_POST['proposer_state'],
                'pan' => $_POST['proposer_pan'],
                'gstin' => $_POST['gstin'],
                'marital_status' => $_POST['status'],
                'is_proposer_insured' => $_POST['is_proposer_insured'],
            ],
            [
                'lead_id' => $_POST['lead_id'],
                'customer_id' => $_POST['customer_id']
            ]
        );
        $data_mem = $this->db->get_where("member_ages", array('member_type' => 1, 'lead_id' => $_POST['lead_id'], 'customer_id' => $_POST['customer_id']))->row_array();
        if ($data_mem) {


            $res = $this->db->update(
                'member_ages',
                [
                    'member_age' => $age,
                ],
                [
                    'member_type' => 1,
                    'lead_id' => $_POST['lead_id'],
                    'customer_id' => $_POST['customer_id']
                ]
            );
        }
        $data = ["status" => 'success',
            "message" => 'Proposer details updated successfully !'];
        echo json_encode($data);

    }

    function axis_pincode_get_state_city()
    {
        $pin_code = $_POST['pincode'];

        $trace_id = $this->session->userdata('trace_id');
        $trace_id = encrypt_decrypt_password($trace_id, 'D'); //"31637406003";

        $pin_code_data = $this->db->select('CITY,STATE')
            ->from('axis_postal_code')
            ->where('PINCODE', $pin_code)
            ->get()
            ->row_array();

        if ($pin_code_data) {


            $data = [
                'status_code' => '200',
                'status' => 'success',
                'pincode_data' => $pin_code_data
            ];

        } else {

            $data = [
                'status_code' => '',
                'status' => 'failed'
            ];

        }

        echo json_encode($data);
    }

    function get_family_construct_data()
    {
        $query = $this->db->query("SELECT a.*,b.member_min_age, b.member_min_age_days, b.member_max_age  FROM family_construct a 
        INNER JOIN master_policy_family_construct b 
        ON a.id = b.member_type_id
        WHERE a.isactive = 1 
        AND b.isactive = 1
        GROUP BY b.member_type_id
        ORDER BY a.id");
//echo $this->db->last_query();
        echo json_encode($query->result_array());
    }

    //upendra - update
    function pincode_insert()
    {
        $pin_code = $_POST['pin_code'];

        $trace_id = $_POST['trace_id'];//$this->session->userdata('trace_id');
        $trace_id = encrypt_decrypt_password($trace_id, 'D'); //"31637406003";
        $customer_id = $_POST['customer_id'];
        $customer_id = encrypt_decrypt_password($customer_id, 'D'); //"31637406003";

        $pin_code_data = $this->db->select('CITY,STATE')
            ->from('axis_postal_code')
            ->where('PINCODE', $pin_code)
            ->get()
            ->row_array();

        if ($pin_code_data) {

            $data = ["pincode" => $pin_code, "city" => $pin_code_data['CITY'], "state" => $pin_code_data['STATE']];

            $this->db->where('trace_id', $trace_id);
            $this->db->update("lead_details", $data);
            if ($customer_id) {
                $this->db->where('customer_id', $customer_id);
                $this->db->update("master_customer", $data);
            }

            $data = [
                'status_code' => '200',
                'status' => 'success',
                'CITY' => $pin_code_data['CITY']
            ];

        } else {

            $data = [
                'status_code' => '',
                'status' => 'failed'
            ];

        }

        echo json_encode($data);
    }

    function insert_pop_city()
    {

        $city_name = $_POST['city_name'];

        $city_data = $this->db->select('STATE,PINCODE')
            ->from('axis_postal_code')
            ->where('LOWER(CITY)', strtolower($city_name))
            ->get()
            ->row_array();

        if ($city_data) {

            $data = [
                'status_code' => '200',
                'status' => 'success',
                'PINCODE' => $city_data['PINCODE']
            ];

        } else {

            $data = [
                'status_code' => '',
                'status' => 'failed'
            ];

        }

        echo json_encode($data);

    }

    function createLead()
    {


        $mobile = $this->input->post('mobile');
        $email = $this->input->post('email');
        $name = $this->input->post('name');
        $gender = $this->input->post('gender');


        $this->db->insert('lead_details', [
            'mobile_no' => $mobile,
            'trace_id' => time(),
            'user_activity' => 1,
            'createdon' => date('Y-m-d H:i:s'),
            'email_id' => $email,
        ]);


        $lead_id = $this->db->insert_id();

        $parts = explode(" ", $name);
        if (count($parts) > 1) {
            $lastname = array_pop($parts);
            $firstname = implode(" ", $parts);
        } else {
            $firstname = $name;
            $lastname = " ";
        }

        if ($gender == 'male') {

            $salutation = 'Mr';
        } else {

            $salutation = 'Ms';
        }

        $this->db->insert('master_customer', [
            'mobile_no' => $mobile,
            'lead_id' => $lead_id,
            'salutation' => $salutation,
            'first_name' => $firstname,
            'last_name' => $lastname,
            'gender' => $gender,
            'full_name' => $name,
            'email_id' => $email,
            'createdon' => date('Y-m-d H:i:s')
        ]);

        $customer_id = $this->db->insert_id();
        // var_dump($customer_id);exit;

        $trace_id = $customer_id . time();
        $this->db->update('lead_details', ['trace_id' => $trace_id], "lead_id = $lead_id");

        $data = [

            'lead_id' => encrypt_decrypt_password($lead_id),
            'customer_id' => encrypt_decrypt_password($customer_id),
            'trace_id' => encrypt_decrypt_password($trace_id)
        ];

        //upendra - update
        //$the_session = array("trace_id" => $trace_id);
        //$this -> session -> set_userdata($the_session);

        echo json_encode(array("status_code" => "200", 'data' => $data));
        exit;
    }

    function getSuminsuredType()
    {
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $existing_members = $this->db->select('id')->from('member_ages')->where(['lead_id' => $lead_id])->get()->result_array();
        $existing_members_count = count($existing_members);
        $data = $this->db->select('suminsured_type_id,suminsured_type')
            ->from('master_suminsured_type')
            ->where('isactive', 1)
            ->get()
            ->result_array();

//print_r($data);
        echo json_encode(array("status_code" => "200", 'data' => $data, "existing_members_count" => $existing_members_count));
        exit;

    }

    function createInsuredtype()
    {

        $SumInsuredtype = $this->input->post('SumInsuredtype');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');

        if (!empty($SumInsuredtype)) {


            $data_SumInsuredtype = array(
                "si_type_id" => $SumInsuredtype
            );

            // update Data into member_ages
            $this->db->where('lead_id', $lead_id);
            $this->db->update("member_ages", $data_SumInsuredtype);


        }
        echo json_encode(array("status_code" => "200"));
        exit;
    }

    function getMasterDisease()
    {
        $data = $this->db->query('select cd_id,disease_name from chronic_disease_master where isactive = 1')->result_array();
        echo json_encode(array("status_code" => "200", 'data' => $data));
        exit;

    }

    function createDisease()
    {

        $disease_type = $this->input->post('disease_type');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $customer_id = encrypt_decrypt_password($this->input->post('customer_id'), 'D');
        $disease_check = $this->input->post('disease_check');
        if (!empty($disease_type)) {
            $query = $this->db->query("Delete  from  chronic_diease_member_data where lead_id ='" . $lead_id . "' ");
            foreach ($disease_type as $key1 => $disease_types) {

                $data_mb_diesease = array(
                    "cd_id" => $disease_types, "lead_id" => $lead_id,
                    "customer_id" => $customer_id, "disease_check" => $disease_check,
                    "created_date" => date("Y-m-d H:i:s")
                );

                // Insert Data into chronic_diease_member_data
                $this->db->insert("chronic_diease_member_data", $data_mb_diesease);


            }
        }
        echo json_encode(array("status_code" => "200"));
        exit;
    }

    function sendOtp()
    {

        $otp = $this->input->post('otp');
        $mobileno = $this->input->post('mobile_no');
        $query = $this->db->get_where('lead_details', array('mobile_no' => $mobileno))->row_array();

        // print_r($_POST);exit;
        $data = [];

        if (!empty($query)) {

            $this->db->update('short_urls', ['otp' => $otp], 'lead_id = ' . $query['lead_id']);

            $customer_query = $this->db->get_where('master_customer', array('lead_id' => $query['lead_id']))->row_array();

            $data = [

                'lead_id' => encrypt_decrypt_password($query['lead_id']),
                'customer_id' => encrypt_decrypt_password($customer_query['customer_id']),
                'trace_id' => encrypt_decrypt_password($query['trace_id'])
            ];
        } else {

            $this->db->insert('lead_details', ['mobile_no' => $mobileno, 'user_activity' => 1]);
            $lead_id = $this->db->insert_id();

            $this->db->insert('master_customer', ['mobile_no' => $mobileno, 'lead_id' => $lead_id]);
            $customer_id = $this->db->insert_id();

            $trace_id = $customer_id . time();
            $this->db->update('lead_details', ['trace_id' => $trace_id], "lead_id = $lead_id");

            $this->db->insert('short_urls', ['otp' => $otp, 'lead_id' => $lead_id]);

            $data = [

                'lead_id' => encrypt_decrypt_password($lead_id),
                'customer_id' => encrypt_decrypt_password($customer_id),
                'trace_id' => encrypt_decrypt_password($trace_id)
            ];
        }

        echo json_encode(array("status_code" => "200", 'data' => $data));
        exit;
    }

    function validateOtp()
    {
        // print_r($_POST);
        //$otp = $this->input->post('otp');
        $lead_id = encrypt_decrypt_password($this->input->post('lead_id'), 'D');
        $query = $this->db->get_where('lead_details', array('lead_id' => $lead_id))->row_array();
        // print_r($query);
        // echo $this->db->last_query();
        echo json_encode(array("status_code" => "200", "data" => $query));
        exit;
    }

    public function invalidCheckOtpAttempt()
    {

        $lead_id = $this->request->post('lead_id');

        $otpVerificationData = $this->db->query("SELECT * from short_urls where lead_id = " . $lead_id)->row_array();
        // $timestamp = date('Y-m-d H:i:s', strtotime($otpVerificationData['last_attempted_at'].'+1 hour'));
        $timestamp = date('Y-m-d H:i:s', strtotime($otpVerificationData['last_attempted_at'] . '+10 minutes'));
        // OTP COUNT ERROR
        if ($otpVerificationData['hits'] < 5) {
            $res = ["status" => 0, "message" => "Invalid OTP", "url" => ""];
        }
        if ($otpVerificationData['hits'] >= 5 && (date('Y-m-d H:i:s') < $timestamp)) {
            $res = ["status" => 0, "message" => "Maximum attempts limit reached, generate new OTP after an hour !!", "url" => ""];
        }
        if (date('Y-m-d H:i:s') >= $timestamp) {
            $updateInvalidCount = $this->db->query("UPDATE short_urls SET hits = 1 where lead_id = " . $lead_id);
            $res = ["status" => 0, "message" => "Invalid OTP", "url" => ""];
        }
        if ($otpVerificationData['hits'] < 5) {
            $updateInvalidCount = $this->db->query("UPDATE short_urls SET hits = hits + 1 where lead_id = " . $lead_id);
        }

        echo json_encode($res);
        exit;
    }

    public function get_partner_products()
    {

        $partner_id = $_POST['partner_id'];
        $partner_id = encrypt_decrypt_password($partner_id, 'D');

        $data = $this->db->select("mp.plan_name, mpo.plan_id, mf.master_policy_id, fc.member_type, mt.code, msi.suminsured_type")
            ->from("master_plan mp")
            ->join("master_policy mpo", "mpo.plan_id = mp.plan_id AND mpo.creditor_id = $partner_id", 'inner')
            ->join("master_policy_family_construct mf", "mf.master_policy_id = mpo.policy_id", 'inner')
            ->join("family_construct fc", "mf.member_type_id = fc.id", 'inner')
            ->join("master_policy_sub_type mt", "mt.policy_sub_type_id = mpo.policy_sub_type_id", 'inner')
            ->join("master_policy_si_type_mapping mpm", "mpm.master_policy_id = mpo.policy_id", 'inner')
            ->join("master_suminsured_type msi", "msi.suminsured_type_id = mpm.suminsured_type_id", 'inner')
            ->where('mp.creditor_id = ' . $partner_id)
            ->group_by("mp.plan_name, mpo.plan_id, mf.master_policy_id, fc.member_type, mt.code, msi.suminsured_type")
            ->order_by("mp.plan_name,mpo.policy_id")
            ->get()->result_array();
        //echo $this->db->last_query();exit;

        $final_data = [];

        if (!empty($data)) {

            foreach ($data as $data_value) {

                $final_data[$data_value['plan_name']][$data_value['master_policy_id']][$data_value['member_type']] = $data_value;
            }

            $result = ['status' => 1, 'data' => $final_data];
        } else {

            $result = ['status' => 0, 'data' => $final_data];
        }

        echo json_encode($result);
        exit;
    }

    public function get_product_details()
    {

        $plan_id = $_POST['plan_id'];
        $plan_id = encrypt_decrypt_password($plan_id, 'D');

        $data = $this->db->select("mp.plan_name, mpo.policy_id, fc.id, fc.member_type, mt.code, mpp.si_premium_basis_id, mpo.is_optional, msr.dependent_on_policy_id")
            ->from("master_plan mp")
            ->join("master_policy mpo", "mpo.plan_id = mp.plan_id", 'inner')
            ->join("master_policy_family_construct mf", "mf.master_policy_id = mpo.policy_id", 'inner')
            ->join("family_construct fc", "mf.member_type_id = fc.id", 'inner')
            ->join("master_policy_sub_type mt", "mt.policy_sub_type_id = mpo.policy_sub_type_id", 'inner')
            ->join("master_policy_si_type_mapping mpm", "mpm.master_policy_id = mpo.policy_id", 'inner')
            ->join("master_policy_mandatory_if_not_selected_rules msr", "msr.master_policy_id = mpo.policy_id AND msr.isactive = 1", 'left')
            ->join("master_policy_premium_basis_mapping mpp", "mpp.master_policy_id = mpo.policy_id", 'inner')
            ->where('mp.plan_id = ' . $plan_id)
            ->group_by("mp.plan_name, mpo.plan_id, mf.master_policy_id, fc.member_type, mt.code")
            ->order_by("mp.plan_name,mpo.policy_id")
            ->get()->result_array();

        //echo $this->db->last_query();exit;
        $query = $result = [];
        foreach ($data as $data_value) {

            //$premium_basis_id[$data_value['si_premium_basis_id']] = $data_value['si_premium_basis_id'];

            if ($data_value['si_premium_basis_id'] == 5) {

                $query[$data_value['member_type']][$data_value['policy_id']] = "master_policy_premium_permile";
            } else if ($data_value['si_premium_basis_id'] == 7) {

                $query[$data_value['member_type']][$data_value['policy_id']] = "master_per_day_tenure_premiums";
            } else {

                $query[$data_value['member_type']][$data_value['policy_id']] = "master_policy_premium";
            }

            $result['plan_name'] = $data_value['plan_name'];
            $result['plan_id'] = encrypt_decrypt_password($plan_id, 'E');
            $result[$data_value['policy_id']]['code'] = $data_value['code'];
            $result[$data_value['policy_id']]['is_dependent'] = $data_value['dependent_on_policy_id'];
            $result[$data_value['policy_id']]['is_optional'] = $data_value['is_optional'];
            $result[$data_value['policy_id']]['family_construct'][$data_value['id']] = $data_value['member_type'];
            $result['family_construct'][$data_value['id']] = $data_value['member_type'];
        }

        $tenure = $deductable = [];
        foreach ($query as $member_type => $policy_table_arr) {

            foreach ($policy_table_arr as $master_policy_id => $table) {

                if (!isset($result[$master_policy_id]['sum_insured'])) {

                    $age_col = ', min_age, max_age';

                    if ($table == 'master_policy_premium') {

                        $sql = "SELECT distinct sum_insured, deductable $age_col FROM $table WHERE master_policy_id = $master_policy_id and isactive = 1 ORDER BY sum_insured";
                    } else {

                        if ($table == 'master_per_day_tenure_premiums') {

                            $age_col = '';
                        }

                        $sql = "SELECT distinct sum_insured, tenure $age_col FROM $table WHERE master_policy_id = $master_policy_id and isactive = 1 ORDER BY sum_insured";
                    }

                    $sum_insured_data = $this->db->query($sql)->result_array();

                    foreach ($sum_insured_data as $sum_insured) {

                        if (isset($sum_insured['tenure']) && !empty($sum_insured['tenure'])) {

                            $tenure[$sum_insured['tenure']] = $sum_insured['tenure'];
                        }

                        if (isset($sum_insured['deductable']) && !empty($sum_insured['deductable'])) {

                            $deductable[$sum_insured['deductable']] = $sum_insured['deductable'];
                        }
                        $result[$master_policy_id]['sum_insured'][$sum_insured['sum_insured']] = $sum_insured['sum_insured'];
                        $result[$master_policy_id]['min_age'] = $sum_insured['min_age'];
                        $result[$master_policy_id]['max_age'] = $sum_insured['max_age'];
                    }
                }
            }
        }

        $result['deductable'] = $deductable;
        $result['tenure'] = $tenure;
        //$result['deductable'] = $deductable;

        if (!empty($result)) {

            $data = ['status' => 1, 'data' => $result];
        } else {

            $data = ['status' => 0, 'data' => []];
        }

        echo json_encode($data);
        exit;
    }

    public function saveCustomerDetails()
    {

        $lead_id = $_POST['lead_id'];
        $customer_id = $_POST['customer_id'];
        $trace_id = $_POST['trace_id'];
        $plan_id = $_POST['plan_id'];

        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $plan_id = encrypt_decrypt_password($plan_id, 'D');

        $data = $this->db->select('mc.lead_id, mc.customer_id')
            ->from('lead_details ld')
            ->join('master_customer mc', 'mc.lead_id = ld.lead_id', 'inner')
            ->where(['ld.lead_id' => $lead_id, 'mc.customer_id' => $customer_id])->get()->result_array();

        if (!empty($data)) {

            $this->db->update('lead_details', ['plan_id' => $plan_id], ['lead_id' => $lead_id]);
            $this->db->update(
                'master_customer',
                [
                    'salutation' => $_POST['salutation'],
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'full_name' => $_POST['first_name'] . ' ' . $_POST['last_name'],
                    'gender' => $_POST['gender'],
                    'dob' => $_POST['proposer_dob'],
                    'email_id' => $_POST['email'],
                    'mobile_no' => $_POST['mobile_no'],
                    'address_line1' => $_POST['address'],
                    'pincode' => $_POST['pincode'],
                    'city' => $_POST['city'],
                    'state' => $_POST['state']
                ],
                [
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id
                ]
            );

            $proposal_data = $this->db->select('proposal_details_id')->from('proposal_details')->where(['lead_id' => $lead_id])->get()->result_array();

            $proposal_details_id = 0;

            if (empty($proposal_data)) {

                $this->db->insert('proposal_details',
                    [
                        'plan_id' => $plan_id,
                        'lead_id' => $lead_id,
                        'trace_id' => $trace_id,
                        'customer_id' => $customer_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_on' => date('Y-m-d H:i:s'),
                        'created_by' => 0
                    ]
                );

                $proposal_details_id = $this->db->insert_id();
            } else {

                $this->db->update(
                    'proposal_details',
                    [
                        'plan_id' => $plan_id,
                        'lead_id' => $lead_id,
                        'trace_id' => $trace_id,
                        'customer_id' => $customer_id,
                        'updated_on' => date('Y-m-d H:i:s'),
                    ],
                    [
                        'proposal_details_id' => $proposal_data[0]['proposal_details_id']
                    ]
                );

                $proposal_details_id = $proposal_data[0]['proposal_details_id'];
            }

            $quote_ids_arr = $this->db->select('master_quote_id')->from('master_quotes')->where(['lead_id', $lead_id])->get()->result_array();

            $quote_ids = [];

            $quote_ids = array_map(function ($arr) {

                return $arr['master_quote_id'];
            }, $quote_ids_arr);

            $response_data = [
                'proposal_details_id' => $proposal_details_id,
                'quote_ids' => !empty($quote_ids) ? join(',', $quote_ids) : ''
            ];

            $result = ['status' => 200, 'Msg' => "Record inserted successfully", 'data' => $response_data];
        } else {

            $result = ['status' => 500, 'Msg' => "No Records found"];
        }

        echo json_encode($result);
        exit;
    }

    function get_family_construct_details()
    {

        $lead_id = encrypt_decrypt_password($_POST['lead_id'], 'D');

        $family_construct =
            $this->db->select('family_construct')
                ->from('master_quotes')
                ->where('lead_id', $lead_id)
                ->get()->row_array();

        $total = explode('-', $family_construct['family_construct']);
        $data['family_construct'] = $total[0] + $total[1];

        $data['self_details'] =
            $this->db->select('*')
                ->from('master_customer')
                ->where('lead_id', $lead_id)
                ->get()->row_array();

        $data['all_family_construct'] =
            $this->db->select('*')
                ->from('family_construct')
                ->where('isactive', 1)
                ->get()->result_array();

        // print_r($data);
        // exit;
        echo json_encode($data);
        exit;

    }

    public function Createinsure_data()
    {
        $arr = [];
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $display = $this->input->post('display');
        $partner_id = $this->input->post('partner_id');
        $si_type_id = $this->input->post('si_type_id');
        if ($partner_id) {
            $query_del = $this->db->query("delete from member_ages where lead_id = '$lead_id'");

        }
        $query = $this->db->query("delete from proposal_policy_member_details where lead_id = '$lead_id'");
        //fetching policy details which member selected
        $policy_detail_data = $this->db->query("select GROUP_CONCAT(mpfc.member_type_id) as member_type_id,mpfc.master_policy_id,pmpd.cover,pmpd.premium,pmpd.total_premium,pmpd.plan_id from policy_member_plan_details as pmpd ,master_policy_family_construct as mpfc where pmpd.policy_id = mpfc.master_policy_id and  pmpd.lead_id = '$lead_id' group by mpfc.master_policy_id")->result_array();
        $validation['status'] = 'success';
        $validationNew=array();
        $data_arr = [];
//var_dump($display);die;
        foreach ($display as $key => $value) {

            //echo json_encode($value);exit;
            $res = $this->db->get_where("family_construct", array("id" => $value['rel']))->row_array()['member_type'];
            if ($value['rel'] == '' || $value['gender'] == '' || $value['first_name'] == '' || $value['dob'] == '') {

                $retObj = ["status" => "error",
                    "message" => $res . " details can not be blank!"];
                echo json_encode($retObj);
                exit;
            }

            $ages = $this->cal_age(date('Y-m-d', strtotime($value['dob'])));

            $age = $ages['age'] ;
            $age_type = $ages['age_type'];
            //  var_dump(array($age_type,$age,$_POST['policy_id'],$value['rel']));
            //update member age
            $q = "update member_ages SET member_age = '" . $age . "' where member_type = '" . $value['rel'] . "' AND lead_id ='" . $lead_id . "'";
            $this->db->query($q);
            if ($partner_id) {
                $rel = $value['rel'];
//                var_dump($policy_detail_data);
                foreach($policy_detail_data as $policy_det) {
                    //echo 1;
                    $validation = $this->validate_member_age($age_type, $age, $policy_det['master_policy_id'], $rel, $res);
                    $validationNew[]=$validation;
                    //  var_dump($validation);
                }

                $data_member_ages = array(
                    'member_type' => $value['rel'],
                    'member_age' => $age,
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'si_type_id' => $si_type_id,


                );


                $this->db->insert('member_ages', $data_member_ages);
                $value['member_ages_id'] = $this->db->insert_id();


            } else {
                $validation = $this->validate_member_age($age_type, $age, $_POST['policy_id'], $value['rel'], $res);


            }
            if ($partner_id) {
                foreach ($validationNew as $valid){
                    if($valid['status']== 'error'){
                        echo json_encode($valid);
                        exit;
                    }
                }
            }else{
                if ($validation['status'] == 'error') {
                    echo json_encode($validation);
                    exit;
                }
            }
            // print_r($validation);

            $data = array(
                'relation_with_proposal' => $value['rel'],
                'policy_member_gender' => $value['gender'],
                'policy_member_first_name' => $value['first_name'],
                'policy_member_last_name' => $value['last_name'],
                'policy_member_dob' => date('Y-m-d', strtotime($value['dob'])),
                'policy_member_age' => $age,
                'policy_member_age_in_months' => $age_type,
                'lead_id' => $lead_id,
                'customer_id' => $customer_id,
                'member_ages_id' => $value['member_ages_id'],

            );

            //print_r($data);

            $this->db->insert('proposal_policy_member_details', $data);
            // print_r($this->db->last_query());die;
        }
//exit;


        $retObj = ["status" => "success",
            "message" => "Data inserted successfully !"];
        echo json_encode($retObj);


        // print_r($display);die;
    }


    public function getAllpremium_det_single($lead_id = 0, $customer_id = 0, $policy_id = 0, $cover = 0,$policy_sub_type_id='')
    {
        // echo$cover;
        $arrayWhere111=array('customer_id' => $customer_id, 'lead_id' => $lead_id);

        $data['member_ages'] = $this->db->select('fc.is_adult,fc.member_type, ma.member_age, ma.si_type_id')
            ->from('member_ages ma')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where($arrayWhere111)
            ->get()
            ->result_array();

        $arr = ['adult_count' => 0, 'child_count' => 0];

        $min_age = 0;
        $max_age = 0;
        $arr_age = [];
        foreach ($data['member_ages'] as $key => $value) {

            if (!isset($si_type)) {
                $si_type = $value['si_type_id'];
            }


            if ($value['is_adult'] == 'Y')
                $arr['adult_count']++;
            else
                $arr['child_count']++;
            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            // if($members){
            // $min_age = $value['min_age'];
            // $max_age = $value['max_age'];
            $min_age = '18';
            $max_age = '55';

            $arr['member_age'] = $value['member_age'];
            $arr['is_adult'] = $value['is_adult'];
            if ($value['is_adult'] == 'Y') {
                array_push($arr_age, $value['member_age']);
            }
            // }
        }
        $max_age = max($arr_age);


        // print_r($plan_details);die;
        $sum_insured = $cover;


        //$policy->basis_id = 4;
        $adult = $arr['adult_count'];
        $child = $arr['child_count'];

        $sum = 0;
        $policy = $this->db->query("select si_premium_basis_id as basis_id,master_policy_id as policy_id from master_policy_premium_basis_mapping   where master_policy_id = '$policy_id' and isactive =1")->row_array();

        if ($policy['basis_id'] == 1) {
            //echo json_encode(111111);exit;
            $premium = $this->getpolicypremiumflat($policy['policy_id'], $sum_insured);

        } else if ($policy['basis_id'] == 2) {
            // echo json_encode(22222);exit;
            $premium = $this->getpolicypremiumfamilyconstruct($policy['policy_id'], $sum_insured, $adult, $child);

        } else if ($policy['basis_id'] == 3) {
            //echo json_encode(33333);exit;
            $age = $max_age;

            $premium = $this->getpolicypremiumfamilyconstructage($policy['policy_id'], $policy['policy_id'], $sum_insured, $adult, $child, $age);


        } else if ($policy['basis_id'] == 4) {
            foreach ($arr_age as $member_age_cal) {

                $age = $member_age_cal;
                $abc = $this->getpolicypremiummemberage($policy['policy_id'], $sum_insured, $age);
                if (!empty($abc)) {
                    foreach ($abc as $rate_data) {
                        $sum = $sum + $rate_data;

                    }
                }
            }
            $premium['amount'] = $sum;

        }else if ($policy['basis_id'] == 5) {
            $this->age = $max_age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );
//echo $max_age;exit;

            $result = $this->apimodel->getPerMileWisePremium2(array_merge($arr, ['number_of_ci' => 0, 'age' => $max_age, "tenure" => $this->tenure]));
            // echo $this->db->last_query();exit;
            //var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }else if ($policy['basis_id'] == 6) {
            $this->age = $max_age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );


            $result = $this->apimodel->getPolicyFamilyDeductable(array_merge($arr, ['adult_count' => $adult, 'child_count' => $child, "deductable" => 200000]));
            //echo $this->db->last_query();
            //   var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }else if ($policy['basis_id'] ==7) {
            $this->age = $max_age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );


            $result = $this->apimodel->getPolicyPerDayTenurePremium(array_merge($arr, ['tenure' => $this->tenure]));
            //echo $this->db->last_query();
            //   var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }
        // echo json_encode(4444);exit;

        return $premium;

    }

    public function getAllpremium($lead_id = 0, $customer_id = 0, $policy_id = 0, $cover = 0)
    {
        if ($this->input->post('policy_id') != '') {

            $lead_id = $this->input->post('lead_id');
            $customer_id = $this->input->post('customer_id');


            $lead_id = encrypt_decrypt_password($lead_id, 'D');
            $customer_id = encrypt_decrypt_password($customer_id, 'D');
            $policy_id = $this->input->post('policy_id');
            $cover = $this->input->post('cover');
            if ($policy_id) {

                $policy_id = encrypt_decrypt_password($policy_id, 'D');
            }
        }

        $data['member_ages'] = $this->db->select('fc.is_adult,fc.member_type, ma.member_age, ma.si_type_id')
            ->from('member_ages ma')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            ->get()
            ->result_array();

//print_r($data);die;
        $arr = ['adult_count' => 0, 'child_count' => 0];

        $min_age = 0;
        $max_age = 0;
        $arr_age = [];
        foreach ($data['member_ages'] as $key => $value) {

            if (!isset($si_type)) {
                $si_type = $value['si_type_id'];
            }


            if ($value['is_adult'] == 'Y')
                $arr['adult_count']++;
            else
                $arr['child_count']++;
            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            // if($members){
            // $min_age = $value['min_age'];
            // $max_age = $value['max_age'];
            $min_age = '18';
            $max_age = '55';

            $arr['member_age'] = $value['member_age'];
            $arr['is_adult'] = $value['is_adult'];
            if ($value['is_adult'] == 'Y') {
                array_push($arr_age, $value['member_age']);
            }
            // }
        }
        $max_age = max($arr_age);


        // print_r($plan_details);die;
        $sum_insured = $cover;


        //$policy->basis_id = 4;
        $adult = $arr['adult_count'];
        $child = $arr['child_count'];

        $sum = 0;
        $policy = $this->db->query("select si_premium_basis_id as basis_id,master_policy_id as policy_id from master_policy_premium_basis_mapping   where master_policy_id = '$policy_id' ")->row_array();
        //print_r($policy);
        if ($policy['basis_id'] == 1) {
            //echo json_encode(111111);exit;
            $premium = $this->getpolicypremiumflat($policy['policy_id'], $sum_insured);

        } else if ($policy['basis_id'] == 2) {
            // echo json_encode(22222);exit;
            $premium = $this->getpolicypremiumfamilyconstruct($policy['policy_id'], $sum_insured, $adult, $child);

        } else if ($policy['basis_id'] == 3) {
            //echo json_encode(33333);exit;
            $age = $max_age;

            $premium = $this->getpolicypremiumfamilyconstructage($policy['policy_id'], $policy['policy_id'], $sum_insured, $adult, $child, $age);


        } else if ($policy['basis_id'] == 4) {
            // echo json_encode(4444);exit;


            if ($arr['is_adult'] == 'Y' && $child == 0) {
                foreach ($arr_age as $member_age_cal) {

                    $age = $member_age_cal;
                    $abc = $this->getpolicypremiummemberage($policy['policy_id'], $sum_insured, $age);
                    if (!empty($abc)) {
                        foreach ($abc as $rate_data) {
                            $sum = $sum + $rate_data;

                        }
                    }
                }
                $premium['amount'] = $sum;
            }


        }
        if ($this->input->post('policy_id') != '') {
            echo json_encode($premium);
        } else {
            if (!empty($premium)) {
                return $premium;
            }

        }

    }

    public function getPolicywiseSumInsured_diff($policy_id, $min, $max)
    {
        $policy = $this->db->query("select si_premium_basis_id as basis_id,master_policy_id as policy_id from master_policy_premium_basis_mapping   where master_policy_id = '$policy_id' ")->row_array();

        if ($policy['basis_id'] == 1) {
            // echo json_encode(111111);exit;
            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy_compare($policy['policy_id'], "master_policy_premium", $min, $max);


        } else if ($policy['basis_id'] == 2) {
            // echo json_encode(22222);exit;

            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy_compare($policy['policy_id'], "master_policy_premium", $min, $max);

        } else if ($policy['basis_id'] == 3) {


            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy_compare($policy['policy_id'], "master_policy_premium", $min, $max);


        } else if ($policy['basis_id'] == 4) {

            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy($policy['policy_id'], "master_policy_premium", $min, $max);
        }
        $arrsum = array();
        foreach ($sumInsure as $data) {
            $arrsum[] = $data;

        }

        return $arrsum;

    }

    public function getPolicywiseSumInsured($policy_id,$adult=null,$child=null,$max_age=null)
    {
        $arrsum = array();
        $policy = $this->db->query("select si_premium_basis_id as basis_id,master_policy_id as policy_id from master_policy_premium_basis_mapping   where master_policy_id = '$policy_id' ")->row_array();
        /*print_r($policy);
        exit;*/
        $where=array();
        if(!is_null($adult)){
            $where['adult_count >=']=$adult;
        }
        if(!is_null($child)){
            $where['child_count >=']=$child;
        }
        if ($policy['basis_id'] == 1) {
            // echo json_encode(111111);exit;
            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy($policy['policy_id'], "master_policy_premium",$where);
//var_dump($sumInsure);exit;

        } else if ($policy['basis_id'] == 2) {
            // echo json_encode(22222);exit;

            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy($policy['policy_id'], "master_policy_premium",$where);
            //echo $this->db->last_query();
        } else if ($policy['basis_id'] == 3) {


            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy($policy['policy_id'], "master_policy_premium",$where);


        } else if ($policy['basis_id'] == 4) {

            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy($policy['policy_id'], "master_policy_premium",$where);
            //echo $this->db->last_query();
            //var_dump($sumInsure);exit;
        }else if ($policy['basis_id'] == 5) {

            $sumInsure = $this->customerapimodel->getSumInsureDataPolicy($policy['policy_id'], "master_policy_premium_permile",$max_age);
            //echo $this->db->last_query();
            //var_dump($sumInsure);exit;
        }

        foreach ($sumInsure as $data) {
            $arrsum[] = $data;

        }

        return $arrsum;

    }

    public function getAllpremium_det($lead_id, $customer_id, $policy_id, $cover, $max_age, $is_adult, $arr_age, $adult, $child, $partner_id, $whereArray, $min_premium, $max_premium,$policy_sub_type_id='')
    {

//print_r($max_age);die;
        $sum_insured = $cover;

        $sum = 0;
        $policy = $this->db->query("select si_premium_basis_id as basis_id,master_policy_id as policy_id from master_policy_premium_basis_mapping   where master_policy_id = '$policy_id'  and isactive=1")->row_array();

        //  print_r($policy); exit;
        if ($policy['basis_id'] == 1) {
            //echo json_encode(111111);exit;

            $premium = $this->getpolicypremiumflat($policy['policy_id'], $sum_insured, $whereArray, $min_premium, $max_premium, $partner_id);

        } else if ($policy['basis_id'] == 2) {
            // echo json_encode(22222);exit;

            $premium = $this->getpolicypremiumfamilyconstruct($policy['policy_id'], $sum_insured, $adult, $child, $partner_id, $whereArray, $min_premium, $max_premium, $partner_id);

        } else if ($policy['basis_id'] == 3) {
            //echo json_encode(33333);exit;
            $age = $max_age;

            $premium = $this->getpolicypremiumfamilyconstructage($policy['policy_id'], $policy['policy_id'], $sum_insured, $adult, $child, $age, $whereArray, $min_premium, $max_premium, $partner_id);


        } else if ($policy['basis_id'] == 4) {
            // echo json_encode(4444);exit;


            if ($is_adult == 'Y' && $child == 0) {
                foreach ($arr_age as $member_age_cal) {

                    $age = $member_age_cal;
                    $abc = $this->getpolicypremiummemberage($policy['policy_id'], $sum_insured, $age, $whereArray, $min_premium, $max_premium);
                    if (!empty($abc)) {
                        foreach ($abc as $rate_data) {
                            $sum = $sum + $rate_data;

                        }
                    }
                }
                $premium['amount'] = $sum;
            }


        }else if ($policy['basis_id'] == 5) {
            $this->age = $max_age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );


            $result = $this->apimodel->getPerMileWisePremium2(array_merge($arr, ['number_of_ci' => 0, 'age' => $max_age, "tenure" => $this->tenure]));
            //echo $this->db->last_query();
            //   var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }else if ($policy['basis_id'] == 6) {
            $this->age = $max_age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );


            $result = $this->apimodel->getPolicyFamilyDeductable(array_merge($arr, ['adult_count' => $adult, 'child_count' => $child, "deductable" => 200000]));
            //echo $this->db->last_query();
            //   var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }else if ($policy['basis_id'] ==7) {
            $this->age = $max_age;
            $this->tenure = 2;
            $arr=array(
                'policy_id' => $policy['policy_id'],
                'sum_insured' => $sum_insured,
                'hospi_cash_group_code' => 'Grp001',
                'policy_sub_type_id' => $policy_sub_type_id,
                'group_code_type' => 1
            );


            $result = $this->apimodel->getPolicyPerDayTenurePremium(array_merge($arr, ['tenure' => $this->tenure]));
            //echo $this->db->last_query();
            //   var_dump($result['rate']);exit;
            $premium['amount']= $result['rate'];
        }
        return $premium;
    }

    public function cal_age($dob)
    {
        $arr = [];
        $today = date("Y-m-d", strtotime(date("Y-m-d") . "-1 days"));
        $diff = date_diff(date_create(date('d-m-Y', strtotime($dob))), date_create($today));
        $age = $diff->format('%y');
        $age_type = 'years';
        if ($age == 0) {
            $age = $diff->format('%a');
            $age_type = 'days';
        }
        //print_R($age);die;
        $arr = ['age' => $age, 'age_type' => $age_type];
        return $arr;
    }

    public function getMember_insure()
    {
        $lead_id = $this->input->post('lead_id');
        $partner_id = $this->input->post('partner_id');
        $customer_id = $this->input->post('customer_id');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $arr = array();
        $data = [];
        $check_data = $this->db->query("SELECT * from proposal_policy_member_details as pd  where pd.lead_id = '$lead_id'")->result_array();
        if (!empty($check_data)) {

            $members_data = $this->db->query("SELECT pd.*,ma.id as member_ages_id ,fc.*,ma.lead_id,ma.customer_id from member_ages as ma LEFT join proposal_policy_member_details as pd ON ma.id = pd.member_ages_id left join family_construct as fc on ma.member_type = fc.id  where ma.lead_id = '$lead_id'")->result_array();
            //print_r($this->db->last_query());die;
            foreach ($members_data as $val_p) {
                $arr['member_type'] = $val_p['member_type'];
                $arr['member_ages_id'] = $val_p['member_ages_id'];
                $arr['id'] = $val_p['id'];
                $arr['policy_member_first_name'] = $val_p['policy_member_first_name'];
                $arr['policy_member_last_name'] = $val_p['policy_member_last_name'];

                $arr['policy_member_dob'] = $val_p['policy_member_dob'];
                if($val_p['policy_member_gender'] !=''){
                    $arr['policy_member_gender'] = $val_p['policy_member_gender'];
                }else{
                    $arr['policy_member_gender'] = $val_p['gender'];
                }

                array_push($data, $arr);
            }
            echo json_encode($data);
            exit;

        } else {

            if ($partner_id) {
                $get_child = $this->db->query("select * from family_construct where is_adult = 'N'")->result_array();
                $arr_kid = [];
                foreach ($get_child as $key1 => $child_det) {
                    $arr_kid[$key1]['member']['construct'] = $child_det['member_type'];

                    $arr_kid[$key1]['member']['member_id'] = $child_det['id'];
                }

                $members = $this->db->query("select ma.id as member_ages_id,ma.is_adult,ma.member_type,fc.gender,fc.id,ma.lead_id,ma.customer_id from Create_quote_member_singlelink as ma left join family_construct as fc on  ma.member_type = fc.member_type where ma.lead_id = '$lead_id'")->result_array();

                foreach ($members as $value) {

                    if ($value['is_adult'] == 'Y') {
                        $arr['id'] = $value['id'];
                    } else {
                        $arr['id'] = $arr_kid;
                    }
                    $arr['member_type'] = $value['member_type'];
                    $arr['member_ages_id'] = $value['member_ages_id'];
                    $arr['is_adult'] = $value['is_adult'];
                    $arr['policy_member_first_name'] = '';
                    $arr['policy_member_last_name'] = '';
                    $arr['policy_member_dob'] = '';
                    if($value['gender'] !=''){
                        $arr['policy_member_gender'] =$value['gender'];
                    }else{
                        $arr['policy_member_gender'] = '';
                    }

                    if ($value['id'] == 1) {
                        $query = $this->db->query("select * from master_customer where lead_id = '$lead_id'")->row_array();
                        $arr['policy_member_first_name'] = $query['first_name'];
                        $arr['policy_member_last_name'] = $query['last_name'];
                        $arr['policy_member_dob'] = $query['dob'];
                        $arr['policy_member_gender'] = $query['gender'];

                    }
                    array_push($data, $arr);

                }


            } else {

                $members = $this->db->query("select ma.id as member_ages_id,fc.gender,fc.is_adult,fc.member_type,fc.id,ma.lead_id,ma.customer_id from member_ages as ma join family_construct as fc where ma.member_type = fc.id and ma.lead_id = '$lead_id'")->result_array();
                foreach ($members as $val) {
                    $arr['member_type'] = $val['member_type'];
                    $arr['member_ages_id'] = $val['member_ages_id'];
                    $arr['id'] = $val['id'];
                    $arr['is_adult'] = $val['is_adult'];
                    $arr['policy_member_first_name'] = '';
                    $arr['policy_member_last_name'] = '';
                    $arr['policy_member_dob'] = '';
                    if($val['gender'] !=''){
                        $arr['policy_member_gender'] =$val['gender'];
                    }else{
                        $arr['policy_member_gender'] = '';
                    }
                    if ($val['id'] == 1) {
                        $query = $this->db->query("select * from master_customer where lead_id = '$lead_id'")->row_array();
                        $arr['policy_member_first_name'] = $query['first_name'];
                        $arr['policy_member_last_name'] = $query['last_name'];
                        $arr['policy_member_dob'] = $query['dob'];
                        $arr['policy_member_gender'] = $query['gender'];

                    }
                    array_push($data, $arr);


                }
            }
//             print_R($data);die;
            echo json_encode($data);
            exit;
            // print_r($data);
            //  print_r($abc);
            // echo json_encode($arr);exit;
            // echo json_encode($members);exit;
        }
        //     echo json_encode($members);exit;


    }

    public function createMembers()
    {
//echo 123;die;
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');
        $data = $this->input->post('data');

        $data = json_decode($data, true);
//        print_r($data);die;
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        //get adult array
        $adultNum = $this->db->query("select group_concat(id) as adultNum from family_construct where isactive = 1 and is_adult='Y'")->row()->adultNum;
        $adultNum = explode(',', $adultNum);

        $existing_members = $this->db->select('id,si_type_id')->from('member_ages')->where(['lead_id' => $lead_id])->get()->result_array();
        $previous_si_type = 0;

        if (count($existing_members) > 0) {

            $previous_si_type = $existing_members[0]['si_type_id'];

            // echo $previous_si_type;exit;

            $existing_members_id = [];

            $existing_members_id = array_map(function ($value) {

                return $value['id'];

            }, $existing_members);

            $this->db->where_in('id', $existing_members_id);
            $this->db->delete('member_ages');


        }

        $array = [];

        $i = 0;
        $is_adult_exist = 0;
        foreach ($data as $key => $value) {
            if (key($value) == 'member_type') {
//                echo $value['member_type'];
                if (in_array($value['member_type'], $adultNum)) {
                    $is_adult_exist = 1;
                }
                $array[$i]['member_type'] = $value['member_type'];
                $i++;
            }
        }

        if ($is_adult_exist == 0) {
            $response['msg'] = 'Atleast One Adult is Mandatory!';
            $response['status'] = 201;;
            echo json_encode($response);
            exit;
        }
        $plan = '0';
        //sum insured type off for self(type - 'Indivisual/family floater')
        if (count($array) < 2) {
            $previous_si_type = 1;
            if ($array[0]['member_type'] == 1) {
                $plan = '1';
            }

        }
        $i = 0;
        foreach ($data as $key => $value) {

            if (key($value) == 'age') {

                $array[$i]['age'] = $value['age'];
                $i++;
            }
        }
        if (count($array) > 1) {
            $previous_si_type = 2;
        }
        if (!empty($array)) {

            $data = [];

            $i = 0;
            foreach ($array as $key => $value) {

                $data[$i] = [
                    'member_type' => $value['member_type'],
                    'member_age' => $value['age'],
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'si_type_id' => $previous_si_type,
                    'created_on' => date('Y-m-d H:i:s'),
                    'updated_on' => date('Y-m-d H:i:s')
                ];


                $i++;
            }


            $this->db->insert_batch('member_ages', $data);

            $exp = [];
            $sel_p = $this->db->query("select * from member_ages where lead_id = $lead_id")->result_array();
            // print_r($sel_p);die;
            foreach ($sel_p as $val_p) {
                array_push($exp, $val_p['id']);

                $arr_p = ['member_ages_id' => $val_p['id']];
                $this->db->where('relation_with_proposal', $val_p['member_type']);
                $this->db->where('lead_id', $lead_id);

                $this->db->update('proposal_policy_member_details', $arr_p);


            }
            $imp = implode(',', $exp);
            $del_p = $this->db->query("delete from proposal_policy_member_details where member_ages_id not in($imp) and lead_id = '$lead_id'");

//die;
            echo json_encode(['status' => '200', 'message' => 'Members added successfully', 'plan' => $plan]);
            exit;
        }
    }

    public function createMembers1()
    {
//echo 123;die;
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');
        $data = $this->input->post('data');

        $data = json_decode($data, true);
        //  print_r($data);die;
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');


        $existing_members = $this->db->select('id,si_type_id')->from('member_ages')->where(['lead_id' => $lead_id])->get()->result_array();
        $previous_si_type = 0;

        if (count($existing_members) > 0) {

            $previous_si_type = $existing_members[0]['si_type_id'];

            // echo $previous_si_type;exit;

            $existing_members_id = [];

            $existing_members_id = array_map(function ($value) {

                return $value['id'];

            }, $existing_members);

            $this->db->where_in('id', $existing_members_id);
            $this->db->delete('member_ages');
        }

        $array = [];

        $i = 0;
        foreach ($data as $key => $value) {

            if (key($value) == 'member_type') {

                $array[$i]['member_type'] = $value['member_type'];
                $i++;
            }
        }
        $plan = '0';
        //sum insured type off for self(type - 'Indivisual/family floater')
        if (count($array) < 2) {
            if ($array[0]['member_type'] == 1) {
                $plan = '1';
            }

        }
        $i = 0;
        foreach ($data as $key => $value) {

            if (key($value) == 'age') {

                $array[$i]['age'] = $value['age'];
                $i++;
            }
        }

        if (!empty($array)) {

            $data = [];

            $i = 0;
            foreach ($array as $key => $value) {

                $data[$i] = [
                    'member_type' => $value['member_type'],
                    'member_age' => $value['age'],
                    'lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'si_type_id' => $previous_si_type,
                    'created_on' => date('Y-m-d H:i:s'),
                    'updated_on' => date('Y-m-d H:i:s')
                ];


                $i++;
            }


            $this->db->insert_batch('member_ages', $data);

//die;
            echo json_encode(['status' => '200', 'message' => 'Members added successfully', 'plan' => $plan]);
            exit;
        }
    }

    public function getQuotePageData()
    {

        // echo 1;exit;
        $data = [];
        $trace_id = $this->input->post('trace_id');
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');
        $ajaxReq = $this->input->post('ajaxReq');

        // $trace_id = "Q1BaUHFEME1wQ3JNRERQQzFjc1hJZz09";
        // $lead_id = "VDJRNnhIWEd4NFZDbVZTd01UZjBHQT09";
        // $customer_id = "N2ZaS2JRdE84RllRMUtxMzIzSENtQT09";
        // $si_type = "VjVnSUhsTHluQWtBYlg2VDNzTThNQT09";

        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');


        /* data from filters via ajax calls */

        $partner_id = $this->input->post('partner_id');

        if ($partner_id) {

            $partner_id = encrypt_decrypt_password($partner_id, 'D');
        }

        $insurer_id = $this->input->post('insurer_id');
        $insurer_idArr=array();
        if (is_array($insurer_id) && count($insurer_id) > 0) {
            foreach ($insurer_id as $item1){
                $insurer_idArr[] = encrypt_decrypt_password($item1, 'D');
            }

        }
        $insurer_id=implode(',',$insurer_idArr);

        $premium = $this->input->post('premium');
        //  exit;
        $cover = $this->input->post('cover');
        $duration = $this->input->post('duration');
        $members = $this->input->post('members');
        $order = $this->input->post('order');
        $si_type = $this->input->post('si_type');

        if ($si_type) {
            $si_type = encrypt_decrypt_password($si_type, 'D');
            //$updateSIType = "UPDATE member_ages SET si_type_id = '".$si_type."' WHERE customer_id = '".$customer_id."' AND lead_id = '".$lead_id."'";
            //$this->db->query($updateSIType);
            //echo json_encode($si_type);exit;
        }
        // $si_type = 2;
        /* data from filters via ajax calls ends */

        /*$member_where = "customer_id = $customer_id AND lead_id = $lead_id";

        if($members){

            $member_where .= " AND fc.member_type IN (".implode(',', $members).")";
        }*/

        $mainFamConstruct=array();
        $QuerAll=$this->db->query("select id,is_adult from family_construct where isactive=1")->result();
        if(count($QuerAll) > 0){
            foreach ($QuerAll as $r){
                $mainFamConstruct[$r->id]=$r->is_adult;
            }
        }


        $data['trace_id'] = $trace_id;
        $data['member_ages'] = $this->db->select('fc.is_adult,fc.id,fc.member_type, ma.member_age, ma.si_type_id,mst.suminsured_type')
            ->from('member_ages ma')
            ->join('master_suminsured_type mst', 'mst.suminsured_type_id = ma.si_type_id')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            ->get()
            ->result_array();
        //  echo $this->db->last_query();exit;

        $arr = ['adult_count' => 0, 'child_count' => 0];

        $min_age = 0;
        $max_age = 0;
        $arr_age = [];
        $member_type = array();
        $selectedMembers=array();//memType
        foreach ($data['member_ages'] as $key => $value) {
            $selectedMembers[]=$value['id'];
            if (!isset($si_type)) {
                $si_type = $value['si_type_id'];
            }
            $is_adult = $value['is_adult'];

            if ($value['is_adult'] == 'Y')
                $arr['adult_count']++;
            if ($value['is_adult'] == 'Y') {
                array_push($arr_age, $value['member_age']);
            } else
                $arr['child_count']++;
            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            $arr['member_age'] = $value['member_age'];
            $arr['is_adult'] = $value['is_adult'];
            // $min_age = $value['min_age'];
            //$max_age = $value['max_age'];
            if ($members) {
                //$min_age = $value['min_age'];
                //$max_age = $value['max_age'];
                $min_age = '18';
                $max_age = '55';


            }
            $member_type[] = $value['member_type'];

        }
        //   $selectedMembers=implode(',',$selectedMembers);
        $adult = $arr['adult_count'];
        $child = $arr['child_count'];
        if (count($arr_age) <= 0) {

            $max_age = 55;
        } else {
            $max_age = max($arr_age);
        }

        $master_policy_id_arr = $this->db->distinct()
            ->select('master_policy_id')
            ->from('master_policy_si_type_mapping')
            ->where(['suminsured_type_id' => $si_type, 'isactive' => 1])
            ->get()
            ->result_array();

        //echo json_encode($this->db->last_query());exit;

        if (!empty($master_policy_id_arr)) {

            $master_policy_ids = $this->get_required_data_from_array($master_policy_id_arr, 'master_policy_id');

            $where = "mp.policy_id IN (" . implode(',', $master_policy_ids) . ") AND mp.isactive = 1 ";
            // $where = "mp.policy_id IN (".implode(',', $master_policy_ids).") AND mp.isactive = 1 ";
            // echo $this->db->last_query();exit;
            if ($si_type == 2) {
                //  $where .= " AND  (mppr.adult_count = ".$arr['adult_count']." AND mppr.child_count = ".$arr['child_count'].")";
            }
            $where .= " AND  (mp.adult_count >= ".$arr['adult_count']." AND mp.child_count >= ".$arr['child_count'].")";

            $whereArray = array();
            $min_premium = 0;
            $max_premium = 0;
            if ($premium) {

                if (strpos($premium, '-') !== false) {

                    $premium_arr = explode('-', $premium);
                    $min_premium = $premium_arr[0];
                    $max_premium = $premium_arr[1];
                    //  $where .=  " AND ((mppr.premium_rate >=".$premium_arr[0] ." AND mppr.premium_rate <= ".$premium_arr[1].") OR (mppr.premium_with_tax >=".$premium_arr[0] ." AND mppr.premium_with_tax <= ".$premium_arr[1]."))  ";
                } else if ($premium == 3000) {
                    $min_premium = $premium;
                    $max_premium = $premium;
                    //  $where .= " AND ((mppr.premium_rate <=".$premium.") OR (mppr.premium_rate <=".$premium."))";
                } else {
                    $min_premium = 0;
                    $max_premium = $premium;
                    //     $where .= " AND ((mppr.premium_rate >=".$premium.") OR (mppr.premium_rate >=".$premium."))";
                }
            }
            $min_cover=0;
            $max_cover=0;
            if ($cover) {

                if (strpos($cover, '-') !== false) {

                    $cover_arr = explode('-', $cover);
                    $min_cover = $cover_arr[0];
                    $max_cover = $cover_arr[1];
                    // $where .=  " AND (mppr.sum_insured >=".$cover_arr[0] ." AND mppr.sum_insured <= ".$cover_arr[1].")";

                } else {
                    $max_cover = $cover;
                    $min_cover = 0;
                    //   $where .= " AND (mppr.sum_insured >=".$cover.")";
                }
            }

            if ($duration) {

                $where .= " AND TIMESTAMPDIFF(YEAR, `mp`.`policy_start_date`, `mp`.`policy_end_date`) >= " . $duration;
            }

            if ($insurer_id) {
                $where .= " AND mp.insurer_id in  (" . $insurer_id .")";
            }

            $order_by = 'mp.insurer_id ASC';
            if ($order) {

                //   $order_by = 'mppr.premium ASC';
            }

            if ($min_age && $max_age) {

                $where .= " AND (mppr.min_age >= $min_age AND mppr.max_age <= $max_age)";
            }

            if ($partner_id) {
                $where .= " AND mc.creditor_id = '" . $partner_id . "'";
//                $where .= " AND mppr.adult_count = '" . $arr['adult_count'] . "' AND mppr.child_count='" . $arr['child_count'] . "'";

            }
            $adultCount=$arr['adult_count'];
            $childCount=$arr['child_count'];
            // $where .= " AND (select group_concat(mpfc.member_type_id) from master_policy_family_construct mpfc where mpfc.master_policy_id = mp.policy_id AND mpfc.isactive = 1) in ( " . $selectedMembers . ")";
            $group_by = 'mp.plan_id';

            $master_policy_arr = $this->db->select("mp.policy_id,mpbm.si_premium_basis_id,mp.plan_id,mp.policy_sub_type_id,mp.insurer_id,mp.policy_start_date,
            mp.policy_end_date,mpp.plan_name,mi.insurer_name, mpp.creditor_id, 
            TIMESTAMPDIFF(YEAR, mp.policy_start_date, mp.policy_end_date) as duration, mc.creaditor_name, mc.creditor_logo,
            group_concat(mpfc.member_type_id) as familyConstraint")
                ->from('master_policy mp')
                ->join('master_plan mpp', 'mp.plan_id = mpp.plan_id AND mpp.isactive = 1')
                ->join('master_insurer mi', 'mi.insurer_id = mp.insurer_id AND mi.isactive = 1')
                ->join('master_policy_family_construct mpfc', 'mpfc.master_policy_id = mp.policy_id AND mpfc.isactive = 1')
                ->join('master_policy_premium_basis_mapping mpbm', 'mpbm.master_policy_id = mp.policy_id AND mpbm.isactive = 1')

//                ->join('master_policy_premium mppr', 'mppr.master_policy_id = mp.policy_id AND mppr.isactive = 1')
                ->join('master_ceditors mc', 'mc.creditor_id = mpp.creditor_id')
                // ->join('master_policy_premium_permile mpppr', 'mpppr.master_policy_id = mp.policy_id AND mpppr.isactive = 1')
                // ->join('master_per_day_tenure_premiums mpdt', 'mpdt.master_policy_id = mp.policy_id AND mpdt.isactive = 1')
                /*->where_in('mp.policy_id', $master_policy_ids)
                ->where(['mp.policy_sub_type_id' => 1, 'mp.isactive' => 1])
                ->where(['mppr.adult_count' => $arr['adult_count'], 'mppr.child_count' => $arr['child_count']])*/
                ->where($where)
                ->order_by($order_by)
                ->group_by($group_by)
                ->get()->result_array();

            $sum = 0;

            $arr_policy = [];
            //  echo $this->db->last_query();
            //   exit;
//               echo json_encode($master_policy_arr);exit;
            //fetch features
            // print_r($master_policy_arr);die;
            $assSum = [];
            foreach ($master_policy_arr as $key => $value) {
                $familyConstraint=$value['familyConstraint'];
                $familyConstraintarr = explode(',', $familyConstraint);
                $familyConstraintarradult=array();
                foreach ($familyConstraintarr as $m){
                    if ($mainFamConstruct[$m] == 'Y'){
                        $familyConstraintarradult[]=$m;
                    }
                }
                $cnt11=count($familyConstraintarradult);
                $available=0;
                $notavial=0;
                foreach ($selectedMembers as $mem){
                    if ($mainFamConstruct[$mem] == 'Y') {
                        if (in_array($mem, $familyConstraintarradult)) {
                            $available++;
                        }else{
                            $notavial++;
                        }
                    }
                }


                if($available > 0 && $notavial ==0){
                    if ($ajaxReq) {
                        $get_policy_wise_suminsured = $this->getPolicywiseSumInsured_diff($value['policy_id'], $min_cover, $max_cover);
                        $whereArray = 1;
                    } else {
                        $whereArray = 0;
                        //  echo $value['policy_id'];exit;
                        $get_policy_wise_suminsured = $this->getPolicywiseSumInsured($value['policy_id'],$adultCount,$childCount);
                        // var_dump($get_policy_wise_suminsured);exit;
                    }
                    /*      echo 'pooja';
                     print_r($get_policy_wise_suminsured);
                          exit;*/
                    $master_policy_arr[$key]['si_premium_basis_id'] = $value['si_premium_basis_id'];
                    $master_policy_arr[$key]['suminsured'] = $get_policy_wise_suminsured;
                    if (!empty($get_policy_wise_suminsured)) {

                        foreach ($get_policy_wise_suminsured as $key1 => $suminsured) {

                            $get_policy_det = $this->getAllpremium_det($lead_id, $customer_id, $value['policy_id'], $suminsured['sum_insured'], $max_age, $is_adult, $arr_age, $adult, $child, $partner_id, $whereArray, $min_premium, $max_premium,$value['policy_sub_type_id']);
                            // var_dump('Pooja');
                            //  print_r($get_policy_det['amount']);die;
                            $get_policy_amt = 0;
                            if (!empty($get_policy_det)) {
                                $get_policy_amt = $get_policy_det['amount'];
                                $master_policy_arr[$key]['suminsured'][$key1]['rate'] = $get_policy_amt;
                            } else {
                                unset($master_policy_arr[$key]['suminsured'][$key1]);
                            }

                        }
                        $master_policy_arr[$key]['suminsured'] = array_values($master_policy_arr[$key]['suminsured']);
                        $qry = "SELECT * FROM features_config where creditor_id = '" . $value['creditor_id'] . "' AND plan_id = '" . $value['plan_id'] . "' AND isactive = 1 limit 5";
                        $master_policy_arr[$key]['features'] = $this->db->query($qry)->result_array();
                    }
                }else{
                    unset($master_policy_arr[$key]);
                }



            }
//            exit;

            //echo '<pre>';
//        print_r($master_policy_arr);die;
            if (!empty($master_policy_arr)) {

                $data['policy_data'] = $master_policy_arr;
                $data['status'] = 200;
                echo json_encode($data);
                exit;
            } else {

                $data['status'] = 400;
                $data['msg'] = 'No policies were found';
                echo json_encode($data);
                exit;
            }
        } else {

            $data['status'] = 401;
            $data['msg'] = 'No policies were found';
            echo json_encode($data);
            exit;
        }
    }

    public function getAll_data_premium()
    {
        $data = [];
        $res = [];
        $trace_id = $this->input->post('trace_id');
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');

        $sum = 0;
        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $policy_id = $this->input->post('policy_id');
        $cover = $this->input->post('cover');
        $plan_id = encrypt_decrypt_password($this->input->post('plan_id'), 'D');
        $partner_id = $this->session->userdata('partner_id_session');
        if ($policy_id) {

            $policy_id = encrypt_decrypt_password($policy_id, 'D');
        }
        $data['member_ages'] = $this->db->select('fc.is_adult,fc.member_type, ma.member_age, ma.si_type_id')
            ->from('member_ages ma')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            ->get()
            ->result_array();
        //  echo $this->db->last_query();exit;

        $arr = ['adult_count' => 0, 'child_count' => 0];

        $min_age = 0;
        $max_age = 0;
        $arr_age = [];
        foreach ($data['member_ages'] as $key => $value) {

            if (!isset($si_type)) {
                $si_type = $value['si_type_id'];
            }


            if ($value['is_adult'] == 'Y')
                $arr['adult_count']++;
            else
                $arr['child_count']++;
            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            // if($members){
            // $min_age = $value['min_age'];
            // $max_age = $value['max_age'];
            $min_age = '18';
            $max_age = '55';

            $arr['member_age'] = $value['member_age'];
            $arr['is_adult'] = $value['is_adult'];
            if ($value['is_adult'] == 'Y') {
                array_push($arr_age, $value['member_age']);
            }
            // }
        }
        $max_age = max($arr_age);

        $plan_details = $this->apimodel->getPolicyPlan($plan_id);

        $sum_insured = $cover;
        foreach ($plan_details as $key => $policy) {
            $policy_id = $policy['policy_id'];
            $sel_policy_plan = $this->db->query("select * from policy_member_plan_details where lead_id = '$lead_id' and policy_id = '$policy_id'")->row_array();
            if (!empty($sel_policy_plan)) {
                $plan_details[$key]['already_avail'] = 1;
            } else {
                $plan_details[$key]['already_avail'] = 0;
            }

            //$policy->basis_id = 4;
            $adult = $arr['adult_count'];
            $child = $arr['child_count'];


            if ($policy['basis_id'] == 1) {
                //echo json_encode(111111);exit;
                $plan_details[$key]['rate'][] = $this->getpolicypremiumflat($policy['policy_id'], $sum_insured, $partner_id);

            } else if ($policy['basis_id'] == 2) {
                // echo json_encode(22222);exit;
                $plan_details[$key]['rate'][] = $this->getpolicypremiumfamilyconstruct($policy['policy_id'], $sum_insured, $adult, $child, $partner_id);

            } else if ($policy['basis_id'] == 3) {
                //echo json_encode(33333);exit;
                $age = $max_age;

                $plan_details[$key]['rate'][] = $this->getpolicypremiumfamilyconstructage($policy['policy_id'], $policy['policy_id'], $sum_insured, $adult, $child, $age, $partner_id);


            } else if ($policy['basis_id'] == 4) {
                // echo json_encode(4444);exit;


                if ($arr['is_adult'] == 'Y' && $child == 0) {
                    foreach ($arr_age as $member_age_cal) {

                        $age = $member_age_cal;
                        $abc = $this->getpolicypremiummemberage($policy['policy_id'], $sum_insured, $age, $partner_id);
                        if (!empty($abc)) {
                            foreach ($abc as $rate_data) {
                                $sum = $sum + $rate_data;

                            }
                        }
                    }
                    $plan_details[$key]['rate'][]['amount'] = $sum;
                }


            }
        }
        $arr_data = [];
        $data_arr = [];

        if (!empty($plan_details)) {
            foreach ($plan_details as $get_plans) {

                if ($get_plans['rate'][0] != 0 && $get_plans['rate'][0]['amount'] != 0) {


                    $data_arr['premium'] = $get_plans['rate'][0]['amount'];
                    $data_arr['is_combo'] = $get_plans['is_combo'];
                    $data_arr['is_optional'] = $get_plans['is_optional'];
                    $data_arr['already_avail'] = $get_plans['already_avail'];
                    $data_arr['policy_id'] = $get_plans['policy_id'];


                    array_push($arr_data, $data_arr);

                }

            }
        }
//print_r($arr_data);exit;
        if (!empty($arr_data)) {

            // print_r($arr_data);exit;
            echo json_encode($arr_data);
            exit;


        }
    }

    public function getAll_data_card()
    {
        $data = [];
        $res = [];
        $trace_id = $this->input->post('trace_id');
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');

        $sum = 0;
        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $policy_id = $this->input->post('policy_id');
        $cover = $this->input->post('cover');
        if ($policy_id) {

            $policy_id = encrypt_decrypt_password($policy_id, 'D');
        }
        $mainFamConstruct=array();
        $QuerAll=$this->db->query("select id,is_adult from family_construct where isactive=1")->result();
        if(count($QuerAll) > 0){
            foreach ($QuerAll as $r){
                $mainFamConstruct[$r->id]=$r->is_adult;
            }
        }
        $data['member_ages'] = $this->db->select('fc.is_adult,fc.member_type,fc.id, ma.member_age, ma.si_type_id')
            ->from('member_ages ma')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            ->get()
            ->result_array();
        //   echo $this->db->last_query();exit;

        $arr = ['adult_count' => 0, 'child_count' => 0];

        $min_age = 0;
        $max_age = 0;
        $arr_age = [];
        $memberType = array();
        foreach ($data['member_ages'] as $key => $value) {

            if (!isset($si_type)) {
                $si_type = $value['si_type_id'];
            }


            if ($value['is_adult'] == 'Y')
                $arr['adult_count']++;
            else
                $arr['child_count']++;
            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            // if($members){
            // $min_age = $value['min_age'];
            // $max_age = $value['max_age'];
            $min_age = '18';
            $max_age = '55';

            $arr['member_age'] = $value['member_age'];
            $arr['is_adult'] = $value['is_adult'];
            if ($value['is_adult'] == 'Y') {
                array_push($arr_age, $value['member_age']);
            }
            $memberType[] = $value['id'];
            // }
        }
        $max_age = max($arr_age);
        $plan_id = $this->input->post('plan_id');
        $plan_details = $this->apimodel->getProductDetailsAll_diff($plan_id);
//            var_dump($plan_details);
//            exit;

        $memberType = implode(',', $memberType);
//        print_r($plan_details);die;
        $sum_insured = $cover;
        foreach ($plan_details as $key => $policy) {
            $policy_id = $policy['policy_id'];
            $sel_policy_plan = $this->db->query("select * from policy_member_plan_details where lead_id = '$lead_id' and policy_id = '$policy_id'")->row_array();
            if (!empty($sel_policy_plan)) {
                $plan_details[$key]['already_avail'][] = 1;
            } else {
                $plan_details[$key]['already_avail'][] = 0;
            }
//        echo $policy['policy_id'];
//        exit;

            //$policy->basis_id = 4;
            $adult = $arr['adult_count'];
            $child = $arr['child_count'];


            if ($policy['basis_id'] == 1) {
                //echo json_encode(111111);exit;
                $plan_details[$key]['rate'][] = $this->getpolicypremiumflat($policy['policy_id'], $sum_insured);

            } else if ($policy['basis_id'] == 2) {
                // echo json_encode(22222);exit;
                $plan_details[$key]['rate'][] = $this->getpolicypremiumfamilyconstruct($policy['policy_id'], $sum_insured, $adult, $child);

            } else if ($policy['basis_id'] == 3) {
                //echo json_encode(33333);exit;
                $age = $max_age;

                $plan_details[$key]['rate'][] = $this->getpolicypremiumfamilyconstructage($policy['policy_id'], $policy['policy_id'], $sum_insured, $adult, $child, $age);


            } else if ($policy['basis_id'] == 4) {
                // echo json_encode(4444);exit;


                if ($arr['is_adult'] == 'Y' && $child == 0) {
                    foreach ($arr_age as $member_age_cal) {

                        $age = $member_age_cal;
                        $abc = $this->getpolicypremiummemberage($policy['policy_id'], $sum_insured, $age);
                        if (!empty($abc)) {
                            foreach ($abc as $rate_data) {
                                $sum = $sum + $rate_data;

                            }
                        }
                    }
                    $plan_details[$key]['rate'][]['amount'] = $sum;
                }


            } else if ($policy['basis_id'] == 5) {
                $age = $max_age;
                $abc = $this->calculatePerMilePremium($policy['policy_id'], $age, $sum_insured, $tenure);
                $plan_details[$key]['rate'][] = $abc;
                //print_r($abc);
            }

        }
        $arr_data = [];
        $data_arr = [];
// get family construct


        if (!empty($plan_details)) {
            foreach ($plan_details as $get_plans) {
//select group_concat(member_type) as member_type from family_construct where id in(1,2,5,6);


                if (array_key_exists('rate', $get_plans) && $get_plans['rate'][0] != 0 && $get_plans['rate'][0]['amount'] != 0) {
                    //var_dump($get_plans['basis_id']);
                    $data_arr['policy_sub_type_name'] = $get_plans['policy_sub_type_name'];
                    $data_arr['is_combo'] = $get_plans['is_combo'];
                    $data_arr['is_optional'] = $get_plans['is_optional'];
                    $data_arr['creditor_name'] = $get_plans['creditor_name'];
                    $data_arr['creditor_logo'] = $get_plans['creditor_logo'];
                    $data_arr['premium'] = $get_plans['rate'][0]['amount'];
                    $data_arr['policy_id'] = $get_plans['policy_id'];
                    $data_arr['already_avail'] = $get_plans['already_avail'];
                    $data_arr['family_construct'] = $get_plans['family_construct'];
                    $familyConstraint=$get_plans['family_construct'];
                    $familyConstraintarr = explode(',', $familyConstraint);
                    $familyConstraintarradult=array();
                    foreach ($familyConstraintarr as $m){
                        if ($mainFamConstruct[$m] == 'Y'){
                            $familyConstraintarradult[]=$m;
                        }
                    }
                    $available=0;
                    $notavial=0;
                    $memberTypearr=explode(',',$memberType);
                    foreach ($memberTypearr as $mem){
                        if ($mainFamConstruct[$mem] == 'Y') {
                            if (in_array($mem, $familyConstraintarradult)) {
                                $available++;
                            }else{
                                $notavial++;
                            }
                        }
                    }
                    // get family construct
                    $member_typeName = '';
                    if ($get_plans['family_construct'] != null && !empty($get_plans['family_construct'])) {
                        $query = $this->db->query("select group_concat(member_type) as member_type from family_construct where id in(" . $get_plans['family_construct'] . ")");
                        if ($this->db->affected_rows() > 0) {
                            $member_typeName = $query->row()->member_type;
                        }
                    }
                    $data_arr['member_typeName'] = $member_typeName;
                    $data_arr['memberTypeSelected'] = $memberType;

                    if ($available > 0 && $notavial == 0){
                        array_push($arr_data, $data_arr);
                    }


                }

            }
        }
//exit;
//print_r($arr_data);exit;
        if (!empty($arr_data)) {

            // print_r($arr_data);exit;
            echo json_encode($arr_data);
            exit;


        }
    }

    public function createPolicy_member_plan()
    {

        $trace_id = $this->input->post('trace_id');
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');

        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $policy_details = $this->input->post('policy_details');

        $policy_id = encrypt_decrypt_password($this->input->post('policy_id'), 'D');
        $plan_id = $this->input->post('plan_id');
        $cover = $this->input->post('cover');
        $premium = $this->input->post('premium');
        $tenure = $this->input->post('tenure');
        $single = $this->input->post('single');
        $total_premium = $this->input->post('total_premium');
        $del = $this->db->query("delete from policy_member_plan_details where lead_id = '$lead_id'");
        if ($single) {
            $arr = ['lead_id' => $lead_id,
                'customer_id' => $customer_id,
                'trace_id' => $trace_id,
                'plan_id' => $plan_id,
                'policy_id' => $policy_id,
                'cover' => $cover,
                'premium' => $premium,
                'total_premium' => $total_premium,


            ];

        } else {
            foreach ($policy_details as $value) {
                $arr = ['lead_id' => $lead_id,
                    'customer_id' => $customer_id,
                    'trace_id' => $trace_id,
                    'plan_id' => $value['plan_id'],
                    'policy_id' => $value['policy_id'],
                    'cover' => $value['cover'],
                    'premium' => $value['premium'],
                    'total_premium' => $value['total_premium'],
                    'additional_plan_flag' => $value['plan_flag']

                ];
            }

            //

        }
        $this->db->insert('policy_member_plan_details', $arr);
        echo json_encode(['status' => '200', 'message' => 'Members policy plan added successfully']);
        exit;


    }

    public function getQuotePageData_bknew()
    {

        // echo 1;exit;
        $data = [];
        $trace_id = $this->input->post('trace_id');
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');

        // $trace_id = "Q1BaUHFEME1wQ3JNRERQQzFjc1hJZz09";
        // $lead_id = "VDJRNnhIWEd4NFZDbVZTd01UZjBHQT09";
        // $customer_id = "N2ZaS2JRdE84RllRMUtxMzIzSENtQT09";
        // $si_type = "VjVnSUhsTHluQWtBYlg2VDNzTThNQT09";

        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');

        /* data from filters via ajax calls */

        $insurer_id = $this->input->post('insurer_id');

        if ($insurer_id) {

            $insurer_id = encrypt_decrypt_password($insurer_id, 'D');
        }

        $premium = $this->input->post('premium');
        $cover = $this->input->post('cover');
        $duration = $this->input->post('duration');
        $members = $this->input->post('members');
        $order = $this->input->post('order');
        $si_type = $this->input->post('si_type');

        if ($si_type) {

            $si_type = encrypt_decrypt_password($si_type, 'D');
        }
        // $si_type = 2;
        /* data from filters via ajax calls ends */

        /*$member_where = "customer_id = $customer_id AND lead_id = $lead_id";

        if($members){

            $member_where .= " AND fc.member_type IN (".implode(',', $members).")";
        }*/

        $data['trace_id'] = $trace_id;
        $data['member_ages'] = $this->db->select('fc.is_adult,fc.member_type, ma.member_age, ma.si_type_id')
            ->from('member_ages ma')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            ->get()
            ->result_array();
        //echo $this->db->last_query();exit;

        $arr = ['adult_count' => 0, 'child_count' => 0];

        $min_age = 0;
        $max_age = 0;

        foreach ($data['member_ages'] as $key => $value) {

            if (!isset($si_type)) {
                $si_type = $value['si_type_id'];
            }


            if ($value['is_adult'] == 'Y')
                $arr['adult_count']++;
            else
                $arr['child_count']++;
            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            if ($members) {
                // $min_age = $value['min_age'];
                // $max_age = $value['max_age'];
                $min_age = '18';
                $max_age = '55';
            }
        }

        $master_policy_id_arr = $this->db->distinct()
            ->select('master_policy_id')
            ->from('master_policy_si_type_mapping')
            ->where(['suminsured_type_id' => $si_type, 'isactive' => 1])
            ->get()
            ->result_array();

        //echo json_encode($this->db->last_query());exit;

        if (!empty($master_policy_id_arr)) {

            $master_policy_ids = $this->get_required_data_from_array($master_policy_id_arr, 'master_policy_id');

            // $where = "mp.policy_id IN (".implode(',', $master_policy_ids).") AND mp.policy_sub_type_id = 1 AND mp.isactive = 1 AND (mppr.adult_count = ".$arr['adult_count']." OR mppr.child_count = ".$arr['child_count'].")";
            $where = "mp.policy_id IN (" . implode(',', $master_policy_ids) . ") AND mp.isactive = 1 ";
            // echo $this->db->last_query();exit;
            if ($si_type == 2) {
                $where .= " AND mp.policy_sub_type_id = 1 AND (mppr.adult_count = " . $arr['adult_count'] . " OR mppr.child_count = " . $arr['child_count'] . ")";
            }

            if ($premium) {

                if (strpos($premium, '-') !== false) {

                    $premium_arr = explode('-', $premium);
                    $where .= " AND ((mppr.premium_rate >=" . $premium_arr[0] . " AND mppr.premium_rate <= " . $premium_arr[1] . ") OR (mppr.premium_with_tax >=" . $premium_arr[0] . " AND mppr.premium_with_tax <= " . $premium_arr[1] . "))  ";
                } else if ($premium == 3000) {

                    $where .= " AND ((mppr.premium_rate <=" . $premium . ") OR (mppr.premium_rate <=" . $premium . "))";
                } else {

                    $where .= " AND ((mppr.premium_rate >=" . $premium . ") OR (mppr.premium_rate >=" . $premium . "))";
                }
            }

            if ($cover) {

                if (strpos($cover, '-') !== false) {

                    $cover_arr = explode('-', $cover);
                    $where .= " AND (mppr.sum_insured >=" . $cover_arr[0] . " AND mppr.sum_insured <= " . $cover_arr[1] . ")";

                } else {

                    $where .= " AND (mppr.sum_insured >=" . $cover . ")";
                }
            }

            if ($duration) {

                $where .= " AND TIMESTAMPDIFF(YEAR, `mp`.`policy_start_date`, `mp`.`policy_end_date`) >= " . $duration;
            }

            if ($insurer_id) {
                $where .= " AND mp.insurer_id = " . $insurer_id;
            }

            $order_by = 'mp.insurer_id ASC';
            if ($order) {

                $order_by = 'mppr.premium ASC';
            }

            if ($min_age && $max_age) {

                $where .= " AND (mppr.min_age >= $min_age AND mppr.max_age <= $max_age)";
            }
            $group_by = 'mp.plan_id';

            $master_policy_arr = $this->db->select("mp.policy_id,mp.plan_id,mp.insurer_id,mp.policy_start_date,mp.policy_end_date,mpp.plan_name,mi.insurer_name,mppr.sum_insured, mppr.premium_rate,mppr.premium_with_tax, mpp.creditor_id, TIMESTAMPDIFF(YEAR, mp.policy_start_date, mp.policy_end_date) as duration, mc.creaditor_name, mc.creditor_logo")
                ->from('master_policy mp')
                ->join('master_plan mpp', 'mp.plan_id = mpp.plan_id AND mpp.isactive = 1')
                ->join('master_insurer mi', 'mi.insurer_id = mp.insurer_id AND mi.isactive = 1')
                ->join('master_policy_premium mppr', 'mppr.master_policy_id = mp.policy_id AND mppr.isactive = 1')
                ->join('master_ceditors mc', 'mc.creditor_id = mpp.creditor_id')
                // ->join('master_policy_premium_permile mpppr', 'mpppr.master_policy_id = mp.policy_id AND mpppr.isactive = 1')
                // ->join('master_per_day_tenure_premiums mpdt', 'mpdt.master_policy_id = mp.policy_id AND mpdt.isactive = 1')
                /*->where_in('mp.policy_id', $master_policy_ids)
                ->where(['mp.policy_sub_type_id' => 1, 'mp.isactive' => 1])
                ->where(['mppr.adult_count' => $arr['adult_count'], 'mppr.child_count' => $arr['child_count']])*/
                ->where($where)
                ->order_by($order_by)
                ->group_by($group_by)
                ->get()->result_array();

            foreach ($master_policy_arr as $key => $value) {
                $sum_insured = $value['sum_insured'];
                $adult = $arr['adult_count'];
                $child = $arr['child_count'];
                $age = 33;
                $plan_details = $this->apimodel->getProductDetailsAll($value['plan_id']);
                foreach ($plan_details as $policy) {
                    //$policy->basis_id = 4;
                    if ($policy->basis_id == 1) {
                        //echo json_encode(111111);exit;
                        $master_policy_arr[$key]['rate'][] = $this->getpolicypremiumflat($policy->policy_id, $sum_insured);

                    } else if ($policy->basis_id == 2) {
                        // echo json_encode(22222);exit;
                        $master_policy_arr[$key]['rate'][] = $this->getpolicypremiumfamilyconstruct($policy->policy_id, $sum_insured, $adult, $child);

                    } else if ($policy->basis_id == 3) {
                        //echo json_encode(33333);exit;
                        $master_policy_arr[$key]['rate'][] = $this->getpolicypremiumfamilyconstructage($policy->policy_id, $policy->policy_id, $sum_insured, $adult, $child, $age);


                    } else if ($policy->basis_id == 4) {
                        // echo json_encode(4444);exit;
                        $master_policy_arr[$key]['rate'][] = $this->getpolicypremiummemberage($policy->policy_id, $sum_insured, $age);

                    }
                }


            }

            // echo json_encode($master_policy_arr);exit;
            //fetch features
            foreach ($master_policy_arr as $key => $value) {
                $qry = "SELECT * FROM features_config where creditor_id = '" . $value['creditor_id'] . "' AND plan_id = '" . $value['plan_id'] . "' AND isactive = 1 limit 5";
                $master_policy_arr[$key]['features'] = $this->db->query($qry)->result_array();
            }
            if (!empty($master_policy_arr)) {

                $data['policy_data'] = $master_policy_arr;
                $data['status'] = 200;
                echo json_encode($data);
                exit;
            } else {

                $data['status'] = 400;
                $data['msg'] = 'No policies were found';
                echo json_encode($data);
                exit;
            }
        } else {

            $data['status'] = 401;
            $data['msg'] = 'No policies were found';
            echo json_encode($data);
            exit;
        }
    }

    function get_required_data_from_array($arr, $key)
    {

        return array_map(function ($inner_arr) use ($key) {

            return $inner_arr[$key];

        }, $arr);
    }

    public function getCustomerDetails()
    {
        $lead_id = $_POST['lead_id'];
        $customer_id = $_POST['customer_id'];
        $trace_id = $_POST['trace_id'];
        $plan_id = $_POST['plan_id'];

        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $plan_id = encrypt_decrypt_password($plan_id, 'D');
        $data['customer_details'] = $this->customerapimodel->getCustomerDetailByid($customer_id);
        $existing_members = $this->db->select('id,member_type')->from('member_ages')->where(['lead_id' => $lead_id, 'member_type' => 1])->get()->result_array();
        $data['member_type'] = (!empty($existing_members[0]['member_type'])) ? $existing_members[0]['member_type'] : 0;;
        echo json_encode($data);
        exit;
    }

    public function getfamily_data_exist()
    {
        $lead_id = $_POST['lead_id'];
        $customer_id = $_POST['customer_id'];
        $nominee_rel = $_POST['nominee_rel'];

        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        if ($nominee_rel) {
            $query = $this->db->query("select fc.id from master_nominee_relations as mnr,family_construct as fc where mnr.name = fc.member_type and mnr.id = '$nominee_rel'")->row_array();
            $rel_id = $query['id'];
            $get_data = $this->db->query("select * from proposal_policy_member_details where lead_id = '$lead_id' and customer_id = '$customer_id' and relation_with_proposal = '$rel_id'")->result_array();

        } else {
            $get_data = $this->db->query("select * from proposal_policy_member_details where lead_id = '$lead_id' and customer_id = '$customer_id'")->result_array();

        }
        echo json_encode($get_data);
        exit;
    }

    public function getQuoteDetails()
    {

        $lead_id = $_POST['lead_id'];
        $customer_id = $_POST['customer_id'];
        $trace_id = $_POST['trace_id'];
        $plan_id = $_POST['plan_id'];
        $master_policy_id = $_POST['master_policy_id'];
        $creditor_id = $_POST['creditor_id'];

        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $plan_id = encrypt_decrypt_password($plan_id, 'D');
        $master_policy_id = encrypt_decrypt_password($master_policy_id, 'D');
        $creditor_id = encrypt_decrypt_password($creditor_id, 'D');

        /*$existing_members = $this->db->select('id,si_type_id')->from('member_ages')->where(['lead_id' => $lead_id])->limit(1)->get()->result_array();

        if(!empty($existing_members)){

            $si_type = $existing_members[0]['si_type_id'];
            $where = "mp.plan_id = $plan_id AND mp.policy_sub_type_id != 1 AND mp.isactive = 1";

            $policy_data_arr = $this->db->select('mp.creditor_id, mp.policy_sub_type_id, mp.insurer_id, mp.policy_start_date, mp.policy_end_date, mp.is_optional, mp.is_combo, mp.premium_type, mp.max_member_count, mpdt.sum_insured, mpdt.premium_rate, mppr.sum_insured, mppr.premium_rate, mpppr.policy_rate, mpppr.numbers_of_ci, mpppr.sum_insured')
            ->from('master_policy mp')
            ->join('master_policy_premium_permile mpppr', 'mpppr.master_policy_id = mp.policy_id AND mpppr.isactive = 1')
            ->join('master_policy_premium mppr', 'mppr.master_policy_id = mp.policy_id AND mppr.isactive = 1')
            ->join('master_per_day_tenure_premiums mpdt', 'mpdt.master_policy_id = mp.policy_id AND mpdt.isactive = 1')
            ->where($where)
            ->get()
            ->result_array();
        }*/

        $data['plan_details'] = $this->customerapimodel->getProductDetailsAll($plan_id);
        $data['features'] = $this->customerapimodel->getfeaturesbyplan($plan_id, $creditor_id);

        $data['master_policy_details'] = $this->customerapimodel->getPolicySubTypePlanCreditor($plan_id, $creditor_id);
        $data['mandatory_if_not_selected'] = [];

        foreach ($data['master_policy_details'] as $policy) {
            // if ($policy->policy_sub_type_id == 1) { // GHI
            //  $data['sum_insured_type_1'] = $this->customerapimodel->getSumInsureData($policy->policy_id);
            // }

            if ($policy->policy_sub_type_id == 2) { // GPA

                $sum_insured_type_2 = $this->customerapimodel->getSumInsureData($policy->policy_id);

                /*if(empty($sum_insured_type_2)){

                    $sum_insured_type_2 = $this->customerapimodel->getSumInsureData($policy->policy_id, 'master_policy_premium_permile');
                }*/

                $data['sum_insured_type_2'] = $sum_insured_type_2;
            }

            if ($policy->policy_sub_type_id == 3) { // Group Critical Illness, GCI

                $sum_insured_type_3 = $this->customerapimodel->getSumInsureData($policy->policy_id);
                /*if(empty($sum_insured_type_3)){

                    $sum_insured_type_3 = $this->customerapimodel->getSumInsureData($policy->policy_id, 'master_policy_premium_permile');
                }*/

                $data['sum_insured_type_3'] = $sum_insured_type_3;

                $data['numbers_of_ci'] = $this->customerapimodel->getNoOfCI($policy->policy_id);
            }

            if ($policy->policy_sub_type_id == 5) { // Super Topup
                $data['sum_insured_type_5_1'] = $this->customerapimodel->getSumInsureData($policy->policy_id);
                $data['sum_insured_type_5_2'] = $this->customerapimodel->getSumInsureDataDeductible($policy->policy_id);
            }


            if ($policy->policy_sub_type_id == 6) { // Hospi Cash
                if ($policy->basis_id == 7) {
                    $data['sum_insured_type_6'] = $this->customerapimodel->getSumInsureData($policy->policy_id, 'master_per_day_tenure_premiums');
                } else {
                    $data['sum_insured_type_6'] = $this->customerapimodel->getSumInsureData($policy->policy_id);
                }
            }

            $data['mandatory_if_not_selected'][$policy->policy_id] = $this->customerapimodel->getdata('master_policy_mandatory_if_not_selected_rules', 'master_policy_id, dependent_on_policy_id', 'master_policy_id = ' . $policy->policy_id);
        }
        $policy_sub_type_id_map = [];

        foreach ($data['plan_details'] as $plandetail) {
            $plandetail->family_construct = $this->customerapimodel->getPolicyFamilyConstruct($plandetail->policy_id);
            $policy_sub_type_id_map[$plandetail->policy_id] = $plandetail->policy_sub_type_code;
        }
        foreach ($data['plan_details'] as $plandetail) {
            $plandetail->policy_premium = $this->customerapimodel->getPolicyPremium($plandetail->policy_id);
        }


        $data['status'] = 200;

        echo json_encode($data);
        exit;
    }

    public function getQuoteCompareData()
    {
        $lead_id = $_POST['lead_id'];
        $customer_id = $_POST['customer_id'];
        $trace_id = $_POST['trace_id'];


        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $trace_id = encrypt_decrypt_password($trace_id, 'D');

        //$plan_arr = $this->input->post('plans');

        //   foreach($plan_arr as $key=>$single_plan){
        //     $plan_arr[$key] = encrypt_decrypt_password($single_plan, "D");
//}

        $covers_arr = $this->input->post('covers');
//print_r($covers_arr);
        $data['member_ages'] = $this->db->select('fc.is_adult,fc.member_type, ma.member_age, ma.si_type_id')
            ->from('member_ages ma')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            ->get()
            ->result_array();
        // echo $this->db->last_query();exit;

        $arr = ['adult_count' => 0, 'child_count' => 0];

        $min_age = 0;
        $max_age = 0;
        $arr_age = [];
        foreach ($data['member_ages'] as $key => $value) {

            if (!isset($si_type)) {
                $si_type = $value['si_type_id'];
            }


            if ($value['is_adult'] == 'Y')
                $arr['adult_count']++;
            else
                $arr['child_count']++;
            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            // if($members){
            // $min_age = $value['min_age'];
            // $max_age = $value['max_age'];
            $min_age = '18';
            $max_age = '55';

            $arr['member_age'] = $value['member_age'];
            $arr['is_adult'] = $value['is_adult'];
            if ($value['is_adult'] == 'Y') {
                array_push($arr_age, $value['member_age']);
            }
            // }
        }
        $max_age = max($arr_age);

        $adult_count = $arr['adult_count'];
        $child_count = $arr['child_count'];
        $response_data = [];
        $total_premium = 0;
        if (!empty($covers_arr)) {
            foreach ($covers_arr as $all_details) {

                $policy_id = encrypt_decrypt_password($all_details['policy_id'], "D");
                $get_sa_data = $this->customerapimodel->getSumInsureData($policy_id);
                foreach ($get_sa_data as $key => $single_sa_data) {
                    // json_decode($single_sa_data, TRUE);
                    $single_sa_data = (array)$single_sa_data;
                    // echo $single_sa_data['sum_insured'];
                    $get_sa_data[$key] = $single_sa_data['sum_insured'];
                }
                $plan_id = $all_details['plan_id'];
                $cover = $all_details['cover'];
                $plan_data = $this->db->select('mpst.policy_sub_type_name,mp.policy_id,mc.creditor_logo,mpl.plan_id, mi.insurer_name, mp.insurer_id, mppr.sum_insured, mppr.premium_rate, mpl.plan_name, mpl.creditor_id, mc.creaditor_name, TIMESTAMPDIFF(YEAR, mp.policy_start_date, mp.policy_end_date) As tenure, mpl.plan_name')
                    ->from('master_plan mpl')
                    ->join('master_ceditors mc', 'mc.creditor_id = mpl.creditor_id')
                    ->join('master_policy mp', 'mp.plan_id = mpl.plan_id AND mp.isactive = 1')
                    ->join('master_insurer mi', 'mi.insurer_id = mp.insurer_id AND mi.isactive = 1')
                    ->join('master_policy_premium mppr', 'mppr.master_policy_id = mp.policy_id AND mppr.isactive = 1')
                    ->join('master_policy_sub_type as mpst', 'mpst.policy_sub_type_id = mp.policy_sub_type_id')
                    ->where('mpl.plan_id', $plan_id)
                    ->where('mppr.sum_insured', $cover)
                    ->get()
                    ->result_array();
                if (!empty($plan_data)) {


                    foreach ($plan_data as $key => $value) {

                        $creditor_id = $value['creditor_id'];
                        $plan_id = $value['plan_id'];
                        $response_data[$plan_id]['creditor_logo'] = $value['creditor_logo'];
                        $response_data[$plan_id]['premium'] = $value['premium_rate'];
                        $response_data[$plan_id]['sum_insured_data'] = $value['sum_insured'];
                        $response_data[$plan_id]['insurer_name'] = $value['insurer_name'];
                        $response_data[$plan_id]['tenure'] = $value['tenure'];
                        $response_data[$plan_id]['plan_name'] = $value['plan_name'];
                        $response_data[$plan_id]['creditor_name'] = $value['creaditor_name'];
                        $response_data[$plan_id]['plan_id'] = $plan_id;
                        $response_data[$plan_id]['policy_id'] = $value['policy_id'];

                        $data['master_policy_details'] = $this->customerapimodel->getPolicySubTypePlanCreditor($plan_id, $creditor_id);
                        $total_premium = 0;
                        foreach ($data['master_policy_details'] as $policy) {


                            // exit;
                            $response_data[$plan_id]['sum_insured'] = $get_sa_data;
                            $response = $this->getAllpremium($lead_id, $customer_id, $policy->policy_id, $value['sum_insured']);


                            if ($response['amount'] != '') {
                                $policy_id_set = $policy->policy_id;
                                $response_data[$plan_id]['add_on'][$policy->policy_id]['policy_name'] = $policy->policy_sub_type_name;
                                if (($policy->is_combo == 0 && $policy->is_optional == 0) || ($policy->is_combo == 1 && $policy->is_optional == 0)) {
                                    $total_premium = $total_premium + $response['amount'];
                                }
                                $sel_policy_plan = $this->db->query("select * from policy_member_plan_details where lead_id = '$lead_id' and policy_id = '$policy_id_set'")->row_array();
                                if (!empty($sel_policy_plan)) {
                                    if ($policy->is_optional == 1) {
                                        $total_premium = $total_premium + $response['amount'];
                                    }

                                    $response_data[$plan_id]['add_on'][$policy->policy_id]['already_avail'] = 1;
                                } else {
                                    $response_data[$plan_id]['add_on'][$policy->policy_id]['already_avail'] = 0;
                                }
                                $response_data[$plan_id]['add_on'][$policy->policy_id]['premium'] = $response['amount'];
                                $response_data[$plan_id]['add_on'][$policy->policy_id]['is_combo'] = $policy->is_combo;
                                $response_data[$plan_id]['add_on'][$policy->policy_id]['is_optional'] = $policy->is_optional;
                            }


                        }
                        $response_data[$plan_id]['total_premium'] = $total_premium;
                    }
                }

            }
        }


        echo json_encode($response_data);
        exit;

    }

    public function updatePremium()
    {
        $plan_sa = $this->input->post('plan_sa');
        echo '5678';
    }

    public function getLeadDetails()
    {

        $lead_id = $_POST['lead_id'];
        $customer_id = $_POST['customer_id'];
        $trace_id = $_POST['trace_id'];
        $plan_id = $_POST['plan_id'];

        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $plan_id = encrypt_decrypt_password($plan_id, 'D');

        $data = $this->db->select('mc.lead_id, mc.customer_id')
            ->from('lead_details ld')
            ->join('master_customer mc', 'mc.lead_id = ld.lead_id', 'inner')
            ->where(['ld.lead_id' => $lead_id, 'mc.customer_id' => $customer_id])->get()->result_array();

        if (!empty($data)) {

            $result = ['status' => 200, 'Msg' => "Record inserted successfully", 'data' => $data];
        } else {

            $result = ['status' => 500, 'Msg' => "No Records found"];
        }

        echo json_encode($result);
    }

    public function getNomineeRelation()
    {
        $data = $this->db->get("master_nominee_relations")->result_array();

        if (!empty($data)) {

            $result = ['status' => 200, 'Msg' => "Records fetched successfully", 'data' => $data];
        } else {

            $result = ['status' => 500, 'Msg' => "No Records found"];
        }

        echo json_encode($result);
    }

    public function updateNomineeDetails()
    {
        $fetchData = $this->db->get_where("proposal_details", array('lead_id' => $_POST['lead_id'], 'customer_id' => $_POST['customer_id'], 'trace_id' => $_POST['trace_id']))->row_array();
        $lname = "";
        $nominee_name = explode(' ', $_POST['nominee_name']);
        $len = count($nominee_name);
        $fname = $nominee_name[0];
        if ($len > 1) {
            $lname = $nominee_name[$len - 1];
        }
        if ($fetchData) {


            $data = $this->db->update(
                'proposal_details',
                [
                    'nominee_relation' => $_POST['nominee_relation'],
                    'nominee_first_name' => $fname,
                    'nominee_last_name' => $lname,
                    'nominee_dob' => date("Y-m-d", strtotime($_POST['nominee_dob'])),
                    'nominee_contact' => $_POST['nominee_contact_number'],
                    'plan_id' => $_POST['plan_id'],
                ],
                [
                    'lead_id' => $_POST['lead_id'],
                    'customer_id' => $_POST['customer_id'],
                    'trace_id' => $_POST['trace_id'],

                ]
            );
        } else {
            $data = [
                'plan_id' => $_POST['plan_id'],
                'lead_id' => $_POST['lead_id'],
                'customer_id' => $_POST['customer_id'],
                'trace_id' => $_POST['trace_id'],
                'nominee_relation' => $_POST['nominee_relation'],
                'nominee_first_name' => $fname,
                'nominee_last_name' => $lname,
                'nominee_dob' => date("Y-m-d", strtotime($_POST['nominee_dob'])),
                'nominee_contact' => $_POST['nominee_contact_number'],
            ];
            $insertDetails = $this->db->insert("proposal_details", $data);
        }
        echo json_encode(true);
    }

    public function getProposalDetails()
    {
        //$trace_id = $this->input->post('trace_id');
        $lead_id = $this->input->post('lead_id');

        // $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');

        // $proposal_details = $this->db->get_where("proposal_policy",["lead_id" =>$lead_id, "trace_id"=> $trace_id ])->result_array();
        $proposal_details = $this->db->get_where("proposal_policy", ["lead_id" => $lead_id])->result_array();

        if (!empty($proposal_details)) {

            $data['proposal_details'] = $proposal_details;
            $data['status'] = 200;
            echo json_encode($data);
            exit;
        } else {

            $data['status'] = 400;
            $data['msg'] = 'No policies were found';
            echo json_encode($proposal_details);
            exit;
        }
    }

    public function getPremium()
    {


        $data = [];
        $trace_id = $this->input->post('trace_id');
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');
        $policy_id = $this->input->post('policy_id');

        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $policy_id = encrypt_decrypt_password($policy_id, 'D');
        $cover = $this->session->userdata('cover');


        $data['trace_id'] = $trace_id;
        $data['member_ages'] = $this->db->select('fc.is_adult,fc.member_type, ma.member_age, ma.si_type_id')
            ->from('member_ages ma')
            ->join('family_construct fc', 'fc.id = ma.member_type')
            ->where(['customer_id' => $customer_id, 'lead_id' => $lead_id])
            ->get()
            ->result_array();
//echo $this->db->last_query();exit;
        $arr = ['adult_count' => 0, 'child_count' => 0];

        $min_age = 0;
        $max_age = 0;
// echo json_encode($data);exit;
        $adult_age = [];
        foreach ($data['member_ages'] as $key => $value) {


            //if(!$si_type)
            $si_type = $value['si_type_id'];
            if ($value['is_adult'] == 'Y') {
                array_push($adult_age, $value['member_age']);
                $arr['adult_count']++;
            } else {
                $arr['child_count']++;
            }

            /*if(!in_array($value['member_type'], [5,6]))
                $arr['adult_count']++;
            else
                $arr['child_count']++;*/
            //if($members){
            // $min_age = $value['min_age'];
            // $max_age = $value['max_age'];
            $min_age = '18';
            $max_age = '55';
            //}

        }

        // echo json_encode($data);exit;
        if ($data['member_ages'][0]['si_type_id'] == 2) {
            $max_age = max($adult_age);
            $where = "mpp.plan_id = '" . $_POST['plan_id'] . "' AND mp.policy_id IN (" . $policy_id . ") AND mp.policy_sub_type_id = 1 AND mp.isactive = 1 AND mppr.adult_count = " . $arr['adult_count'] . " AND mppr.child_count = " . $arr['child_count'] . " and mppr.sum_insured = '" . $this->input->post('cover') . "' ";
        } else {
            $where = "mpp.plan_id = '" . $_POST['plan_id'] . "' AND mp.policy_id IN (" . $policy_id . ") AND mp.isactive = 1 AND mppr.sum_insured = '" . $this->input->post('cover') . "' ";
        }


        $master_policy_arr = $this->db->select("mppr.min_age,mppr.max_age,mp.policy_id,mp.plan_id,mp.insurer_id,mp.policy_start_date,mp.policy_end_date,mpp.plan_name,mi.insurer_name,mppr.sum_insured, mppr.premium_rate, mpp.creditor_id, TIMESTAMPDIFF(YEAR, mp.policy_start_date, mp.policy_end_date) as duration, mc.creaditor_name, mc.creditor_logo")
            ->from('master_policy mp')
            ->join('master_plan mpp', 'mp.plan_id = mpp.plan_id AND mpp.isactive = 1')
            ->join('master_insurer mi', 'mi.insurer_id = mp.insurer_id AND mi.isactive = 1')
            ->join('master_policy_premium mppr', 'mppr.master_policy_id = mp.policy_id AND mppr.isactive = 1')
            ->join('master_ceditors mc', 'mc.creditor_id = mpp.creditor_id')
            ->where($where)
            ->get()->result_array();
//            echo json_encode($this->db->last_query());exit;
//       var_dump($master_policy_arr);exit;
        if (!empty($master_policy_arr)) {
            //check if premium type is member age
            $sibasis = $this->db->get_where("master_policy_premium_basis_mapping", ["master_policy_id" => $policy_id])->row_array()['si_premium_basis_id'];
            //echo json_encode($sibasis);exit;
            if ($sibasis == 4) {

                $premium = 0;
                foreach ($data['member_ages'] as $key => $value) {
                    $member_age = $value['member_age'];
                    $premiumData = $this->db->get_where("master_policy_premium", ["sum_insured" => $this->input->post('cover'), "master_policy_id" => $policy_id])->result_array();


                    foreach ($premiumData as $valueage) {
                        if ($member_age >= $valueage['min_age'] && $member_age <= $valueage['max_age']) {
                            $premium = $premium + $valueage['premium_rate'];
                        }
                    }
                }
                //echo json_encode($premium);exit;
                $master_policy_arr[0]['premium_rate'] = $premium;
            } else if ($sibasis == 3) {

                $premium = 0;
                foreach ($master_policy_arr as $value) {
                    // echo json_encode($max_age.'---'.$value['min_age'].'----'.$value['max_age']);//exit;
                    if ($max_age >= $value['min_age'] && $max_age <= $value['max_age']) {
                        $premium = $value['premium_rate'];
                    }
                }
                $master_policy_arr[0]['premium_rate'] = $premium;
                $master_policy_arr[0]['total_premium'] = $premium;
            }

            //fetch premium from policy_member_plan_details
            $res = $this->db->get_where('policy_member_plan_details', ["customer_id" => $customer_id])->result_array();
            //     echo json_encode($this->db->last_query());exit;
            if (!empty($res)) {
                $prem = 0;
                $total_premium = 0;
                //var_dump($res);
                foreach ($res as $key => $valprem) {
                    if ($valprem['additional_plan_flag'] == 0) {
                        $prem += $valprem['premium'];
                    }
                    $total_premium = $valprem['total_premium'];
                }
                $master_policy_arr[0]['premium_rate'] = $prem;
                $master_policy_arr[0]['total_premium'] = $total_premium;

            }
//                echo '<pre>';
            //    print_r($master_policy_arr);
//                 echo json_encode($master_policy_arr);exit;
            $data['policy_data'] = $master_policy_arr[0];
            $data['status'] = 200;
            echo json_encode($data);
            exit;
        } else {

            $data['status'] = 400;
            $data['msg'] = 'No policies were found';
            echo json_encode($data);
            exit;
        }

    }

    public function getNomineeDetails()
    {
        $trace_id = $this->input->post('trace_id');
        $lead_id = $this->input->post('lead_id');
        $customer_id = $this->input->post('customer_id');

        $trace_id = encrypt_decrypt_password($trace_id, 'D');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');
        $customer_id = encrypt_decrypt_password($customer_id, 'D');
        $nq = "SELECT *,mnr.name AS nominee_relation_name
                FROM proposal_details AS pd,master_nominee_relations AS mnr
                WHERE pd.nominee_relation = mnr.id AND pd.`lead_id` = '" . $lead_id . "' AND pd.`customer_id` = '" . $customer_id . "' 
                AND pd.`trace_id` = '" . $trace_id . "' AND pd.`plan_id` = '" . $_POST['plan_id'] . "'";
        //$fetchData = $this->db->get_where("proposal_details", array('lead_id' => $lead_id, 'customer_id' => $customer_id,'trace_id' => $trace_id,'plan_id' => $_POST['plan_id']))->row_array();
        $fetchData = $this->db->query($nq)->row_array();
        // echo json_encode($this->db->last_query());exit;
        if ($fetchData) {
            $data['policy_data'] = $fetchData;
            $data['status'] = 200;
        } else {
            $data['status'] = 400;
            $data['msg'] = 'No policies were found';
        }
        echo json_encode($data);
        exit;
    }

    public function validate_member_age($age_type, $age, $policy_id, $member_type_id, $memberType = '')
    {
        $get_arr = [];
        $data = array();
        $q = "SELECT * FROM master_policy_family_construct WHERE master_policy_id = '" . $policy_id . "' AND member_type_id = " . $member_type_id . " AND isactive = 1";

        $policyAge_details = $this->db->query($q)->row_array();
//            var_dump($policyAge_details);
        if ($this->db->affected_rows() > 0) {
            $q_rel = "SELECT * FROM family_construct WHERE id = '$member_type_id'";

            $policyDet = $this->db->query($q_rel)->row_array();

            if ($policyDet['is_adult'] == 'N') {

                if ((int)$age >= (int)$policyAge_details['member_min_age_days'] && $age_type=='days' ) {

                    $data['status'] = 'success';
                    $data['message'] = "success";
                } else if((int)$age <= (int)$policyAge_details['member_max_age'] && $age_type=='years' ){
                    $data['status'] = 'success';
                    $data['message'] = "success";
                }else {
                    //echo "in";exit;
                    $data['status'] = 'error';

                    if ($member_type_id == 5 || $member_type_id == 6) {
                        $data['message'] = "Sorry this plan covers kids from the age of " . $policyAge_details['member_min_age_days'] . " days to " . $policyAge_details['member_max_age'] . " years.";
                    } else {
                        $data['message'] = "Sorry this plan covers adults from the age of " . $policyAge_details['member_min_age'] . " years to " . $policyAge_details['member_max_age'] . " years.";
                    }


                }
            }

            else {
//
                if ((int)$age >= (int)$policyAge_details['member_min_age'] && (int)$age <= (int)$policyAge_details['member_max_age']) {
                    $data['status'] = 'success';
                    $data['message'] = "success";

                } else {
                    //echo "in";exit;
                    $data['status'] = 'error';

                    if ($member_type_id == 5 || $member_type_id == 6) {
                        $data['message'] = "Sorry this plan covers kids from the age of " . $policyAge_details['member_min_age'] . " year(s) to " . $policyAge_details['member_max_age'] . " years.";
                    } else {
                        $data['message'] = "Sorry this plan covers adults from the age of " . $policyAge_details['member_min_age'] . " years to " . $policyAge_details['member_max_age'] . " years.";
                        //return $data;
                    }
                    //  print_r($data);
                    // return $data;

                }
            }
        }

        return $data;
    }
    public	 function verify_request_customer($token)
    {
        // Get all the headers
        //$headers = $this->input->request_headers();
        // Extract the token
        //$token = $headers['Authorization'];
        // Use try-catch
        // JWT library throws exception if the token is not valid
        try {
            // Validate the token
            // Successfull validation will return the decoded user data else returns false
            $data = AUTHORIZATION::validateToken($token);
            if ($data === false) {
                return json_encode(array("status_code" => "401", "Metadata" => array("Message" => "Unauthorized Access!"), "Data" => NULL));
                exit;
            } else {
                return $data;
            }
        } catch (Exception $e) {

            // Token is invalid
            // Send the unathorized access message

            return json_encode(array("status_code" => "401", "Metadata" => array("Message" => "Unauthorized Access!"), "Data" => NULL));
            exit;
        }
    }

    public function policyIssuance()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $api_data = json_decode(file_get_contents('php://input') , true);
        if(!empty($api_data)){
            $checkToken =$this->verify_request_customer($api_data['PolicyCreationRequest']['utoken']);
            if (!empty($checkToken->username)) {
                $req_data['lead_id']= $api_data['PolicyCreationRequest']['LeadId'];
                $req_data['txt_date']= $api_data['PolicyCreationRequest']['TransactionRcvdDate'];
                $req_data['txt_number']= $api_data['PolicyCreationRequest']['TransactionNumber'];
                $update_payment_status = $this->updateProposalStatus($req_data);
                $policyResponse = $update_payment_status;
                $policyResponse1=array();
                foreach ($policyResponse['PolicyCreationResponse'] as $row){
                    unset($row['gross_premium']);
                    $policyResponse1[]=$row;
                }
                $lead_id = encrypt_decrypt_password($api_data['PolicyCreationRequest']['LeadId']);
                $policyResponse1['COI_URL']='http://fyntunecreditoruat.benefitz.in/quotes/success_view/'.$lead_id;
                $sendMail= $this->sendMail($policyResponse1['COI_URL']);
                $policyResponse1['mail']=$sendMail;
                print_R(json_encode($policyResponse1));die;
            }
            else{
                echo $checktoken;
            }


        }

    }

    function sendMail($url){
        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtppro.zoho.in';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->SMTPSecure = "tls";
            $mail->Username   = 'noreply@fyntune.com';                     //SMTP username
            $mail->Password   = 'Fyntune9001#';                               //SMTP password
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('noreply@fyntune.com', 'Mailer');
            $mail->addAddress('poojalote123@gmail.com', 'Test');     //Add a recipient

            $body="<p>Hello,</p>
<p>Please click on below given link to get your certificate of Issuance.</p>
<p>".$url."</p>
<p>Regards,<br>
Fyntune Team.
</p>
";
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Here is the subject';
            $mail->Body    = $body;
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            return 'Message has been sent';
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function updateProposalStatus($data = '')
    {
        if(!empty($data))
        {
            $_POST['lead_id'] = $data['lead_id'];
            $txt_date = $data['txt_date'];
            $txt_num = $data['txt_number'];
            $api = 'API';
        }
        else
        {
            $txt_date = date("Y-m-d H:i:S");
            $txt_num = $_POST['pg_response']['razorpay_payment_id'];
            $api = '';
        }
        $lead_query = $this->db->query("select l.lead_id from lead_details as l,proposal_policy as p,proposal_payment_details as ppd where l.lead_id = p.lead_id and l.lead_id = ppd.lead_id and l.lead_id = '".$_POST['lead_id']."'")->row_array();
        if(!empty($lead_query))
        {
            $this->db->query("UPDATE proposal_policy SET status = 'Payment-Done' WHERE lead_id = '" . $_POST['lead_id'] . "'");
            $this->db->query("UPDATE lead_details SET status = 'Customer-Payment-Received' WHERE lead_id = '" . $_POST['lead_id'] . "'");
            $this->db->query("UPDATE proposal_payment_details SET payment_status = 'Success', proposal_status = 'PaymentReceived' ,payment_date = '" . $txt_date . "',transaction_date = '" . $txt_date . "',transaction_number = '" . $txt_num . "', remark = 'PaymentReceived' WHERE lead_id = '" . $_POST['lead_id'] . "'");
            //echo json_encode("UPDATE lead_details SET status = 'Customer-Payment-Received' WHERE lead_id = '".$_POST['lead_id']."'");exit;
            $res = $this->saveApiProposalResponse($_POST['lead_id'],$api);
            if($api = 'API')
            {
                return $res;
            }else{
                echo json_encode($res);
            }
        }
        else{
            $response = ["StatusCode" =>400,"Status"=>"Error","Message"=>"Data Not Found","PolicyCreationResponse"=>""];
            return $response;

        }
        exit;
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

    function updateProposalPolicyStatus($column_name, $column_value, $status)
    {
        $proposalPolicy = $this->db->query("SELECT proposal_policy_id FROM proposal_policy WHERE `$column_name` = '$column_value'")->row_array();
        if ($proposalPolicy > 0) {
            $updateArr = ["status" => $status];
            $this->db->where($column_name, $column_value);
            $this->db->update("proposal_policy", $updateArr);
            $insert_id = $proposalPolicy['proposal_policy_id'];
        }
        return true;
    }

    function calculatePerMilePremium($policy_id, $age, $sum_insured, $tenure)
    {


        $request = [
            'policy_id' => $policy_id,
            'age' => $age,
            'sum_insured' => $sum_insured,
            'tenure' => $tenure,
        ];

        if ($policy_sub_type_id == 3) {
            $request['number_of_ci'] = !empty($number_of_ci) ? $number_of_ci : 0;
        }

        $result = $this->apimodel->getPerMileWisePremium($request);

        // If cover is individual append the member type to the policy code


        return $result['rate'];
    }

    function saveApiProposalResponse($lead_id,$api)
    {
        $api_status = 0;
        $policyDataArr = $this->db->get_where("proposal_policy", ["lead_id" => $lead_id])->result_array();
        foreach ($policyDataArr as $key => $policyData) {
            $policy_subtype_name = $this->db->get_where("master_policy_sub_type", ["policy_sub_type_id" => $policyData['policy_sub_type_id']])->row_array();
            //	print_R($policy_subtype_name);die;
            $customer_id = $this->db->get_where("master_customer", ["lead_id" => $lead_id])->row_array()['customer_id'];
            $CertificateNumber = $this->generate_coi($policy_subtype_name['code']);
            $startDate = date('Y-m-d');
            $EndDate = date('Y-m-d', strtotime($startDate . ' + 364 days'));


            $GrossPremium = isset($policyData['premium_amount']) ? $policyData['premium_amount'] : '';


            $lead_id = isset($policyData['lead_id']) ? $policyData['lead_id'] : '';
            $proposal_policy_id = isset($policyData['proposal_policy_id']) ? $policyData['proposal_policy_id'] : '';
            $policy_sub_type_id = isset($policyData['policy_sub_type_id']) ? $policyData['policy_sub_type_id'] : '';
            $customer_id = isset($customer_id) ? $customer_id : '';
            $master_policy_id = isset($policyData['master_policy_id']) ? $policyData['master_policy_id'] : '';
            $ProposalNumber = isset($policyData['proposal_no']) ? $policyData['proposal_no'] : '';

            $request_arr = array(
                "lead_id" => $lead_id,
                "certificate_number" => $CertificateNumber,
                "proposal_no" => $ProposalNumber,
                "policy_sub_type_id" => $policy_sub_type_id,
                "master_policy_id" => $master_policy_id,
                "gross_premium" => $GrossPremium,
                //"status" => $statusRes,
                "start_date" => date('Y-m-d H:i:s', strtotime($startDate)),
                "end_date" => date('Y-m-d H:i:s', strtotime($EndDate)),
                "created_date" => date('Y-m-d H:i:s'),
                "proposal_policy_id" => $proposal_policy_id,
                "customer_id" => $customer_id,
            );

            // echo json_encode($request_arr);exit;

            $apiProposalResponse = $this->db->query("SELECT pr_api_id FROM api_proposal_response 
            WHERE lead_id='$lead_id' AND proposal_policy_id='$proposal_policy_id'
            AND customer_id='$customer_id' AND master_policy_id='$master_policy_id'
            AND policy_sub_type_id='$policy_sub_type_id'")->row_array();


            if ($apiProposalResponse > 0) {
                $api_status = 1;
                /*  $this->db->where("lead_id", $lead_id);
                  $this->db->where("proposal_policy_id", $proposal_policy_id);
                  $this->db->where("customer_id", $customer_id);
                  $this->db->where("master_policy_id", $master_policy_id);
                  $this->db->where("policy_sub_type_id", $policy_sub_type_id);
                  $this->db->update("api_proposal_response", $request_arr);
                  $insert_id = $apiProposalResponse['pr_api_id'];*/

            } else {
                $api_status = 0;
                //echo json_encode('1111');exit;
                $this->db->insert("api_proposal_response", $request_arr);

                $insert_id = $this->db->insert_id();
            }
            $policyIssuanceResponse[] =['plan_name' => $policy_subtype_name['policy_sub_type_name'],'certificate_number'=>$CertificateNumber,'gross_premium'=>$GrossPremium,'policy_start_date'=> date('Y-m-d H:i:s', strtotime($startDate)),'policy_expiry_date'=>date('Y-m-d H:i:s', strtotime($startDate))];
        }
        if($api = 'API'){
            if($api_status == 0)
            {
                $response = ["StatusCode" =>200,"Status"=>"Success","Message"=>"Success","PolicyCreationResponse"=>$policyIssuanceResponse];
            }else{
                $response = ["StatusCode" =>301,"Status"=>"Error","Message"=>"Policy Already Generated","PolicyCreationResponse"=>$policyIssuanceResponse];

            }

            return $response;


        }
        else{
            return $CertificateNumber;
        }
        //$updateProposalPolicyStatus = $this->updateProposalPolicyStatus('proposal_policy_id', $proposal_policy_id, "Full-Quote-Done");
    }

    public function getCOidetails()
    {

        //echo json_encode("SELECT api.certificate_number,pd.transaction_number FROM proposal_payment_details AS pd,api_proposal_response AS api WHERE pd.lead_id = api.lead_id and pd.lead_id = ".$_POST['lead_id']);exit;
        $data = $this->db->query("SELECT GROUP_CONCAT(DISTINCT(api.certificate_number)) certificate_number,pd.transaction_number FROM proposal_payment_details AS pd,api_proposal_response AS api WHERE pd.lead_id = api.lead_id and pd.lead_id = " . $_POST['lead_id'] . " GROUP BY pd.transaction_number")->row_array();
        // echo $this->db->last_query();exit;
        if (!empty($data)) {

            $result = ['status' => 200, 'Msg' => "Records fetched successfully", 'data' => $data];
        } else {

            $result = ['status' => 500, 'Msg' => "No Records found"];
        }

        echo json_encode($result);
    }

    public function getCOiinfo()
    {

        // echo json_encode($_POST['lead_id']);exit;
        //  echo json_encode("SELECT api.certificate_number,pd.transaction_number FROM proposal_payment_details AS pd,api_proposal_response AS api WHERE pd.lead_id = api.lead_id and pd.lead_id = ".$_POST['lead_id']);exit;

        $data = $this->db->query("SELECT pd.nominee_first_name,(select full_name from master_customer mc where mc.customer_id=pd.customer_id) as cust_details,pd.nominee_last_name,mpst.policy_sub_type_name,mpl.plan_name, mc.creaditor_name,mp.policy_number, mp.policy_sub_type_id,GROUP_CONCAT(DISTINCT(api.certificate_number)) certificate_number,api.start_date,api.end_date,api.TransactionID FROM lead_details AS ld, api_proposal_response AS api,master_policy AS mp,master_ceditors AS mc,master_plan AS mpl,master_policy_sub_type AS mpst,proposal_details AS pd WHERE ld.lead_id = api.lead_id AND mp.policy_id= api.master_policy_id AND mp.creditor_id=mc.creditor_id AND mp.plan_id = mpl.plan_id AND mpst.policy_sub_type_id = mp.policy_sub_type_id AND pd.plan_id = mp.plan_id AND pd.lead_id = ld.lead_id AND ld.lead_id = " . $_POST['lead_id'] . " GROUP BY pd.transaction_number")->row_array();
//echo $this->db->last_query();exit;
        $premium_details=$this->db->query("select sum(premium) as premium ,sum(premium_with_tax) as premium_with_tax from master_quotes where lead_id=" . $_POST['lead_id'])->row();
        if($premium_details->premium != null){

        }else{
            $premium_details=$this->db->query("select sum(premium) as premium ,(total_premium) as premium_with_tax from policy_member_plan_details where lead_id=" . $_POST['lead_id'])->row();
        }
        if (!empty($data)) {

            $result = ['status' => 200, 'Msg' => "Records fetched successfully", 'data' => $data,'premium_details'=>$premium_details];
        } else {

            $result = ['status' => 500, 'Msg' => "No Records found"];
        }

        echo json_encode($result);
    }

    function getpolicypremiumflat($id, $suminsured, $whereArray = null, $min_premium = null, $max_premium = null, $partner_id = null)
    {
        $ptype = $this->db->get_where("master_policy", array("policy_id" => $id))->row()->premium_type;

        if ($ptype == 1) {
            $dt = "master_policy_id =$id AND sum_insured = $suminsured";
        } else {
            $dt .= "master_policy_id =$id ";

        }
        if ($whereArray == 1) {
            if ($min_premium != 0) {
                if ($min_premium == 3000) {
                    $dt .= " AND ((premium_rate <=" . $min_premium . ") OR (premium_rate <=" . $max_premium . "))";
                } else {
                    $dt .= " AND ((premium_rate >=" . $min_premium . " AND premium_rate <= " . $max_premium . ") OR (premium_with_tax >=" . $min_premium . " AND premium_with_tax <= " . $max_premium . "))  ";

                }
            } else {

                $dt .= " AND ((premium_rate >=" . $max_premium . ") OR (premium_rate >=" . $max_premium . "))";
            }
        }
        $rate = $this->apimodel->getdata1("master_policy_premium", "*", $dt);
        if ($partner_id) {
            $rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = $id AND sum_insured = $suminsured");

        }
        if (!empty($rate)) {
            if ($rate[0]->premium_rate == '') {
                $premium = $rate[0]->premium_with_tax;
            } else {
                $premium = $rate[0]->premium_rate;
            }
            if ($ptype == 1) {

                $r['amount'] = $premium;
            } else {
                $r['amount'] = ($suminsured / 1000) * $premium;
            }

            if ($rate[0]->is_taxable) {
                $r['tax'] = $r['amount'] * $this->config->item('tax') / 100;
            }
        } else {
            $r = 0;
        }
        return $r;
    }

    function getpolicypremiumfamilyconstruct($policy, $sum_insured, $adult, $child, $partner_id = null, $whereArray = null, $min_premium = null, $max_premium = null)
    {

        //$ptype = $this->db->get_where("master_policy",array("master_policy_id"=> $policy))->row()->premium_type;

        $ptype = $this->db->get_where("master_policy", array("policy_id" => $policy))->row()->premium_type;
        if ($ptype == 1) {
            $dt = "master_policy_id = $policy AND sum_insured = $sum_insured AND adult_count = $adult AND child_count = $child";
        } else {
            $dt .= "master_policy_id = $policy AND sum_insured = $sum_insured";

        }
        if ($whereArray == 1) {
            if ($min_premium != 0) {
                if ($min_premium == 3000) {
                    $dt .= " AND ((premium_rate <=" . $min_premium . ") OR (premium_rate <=" . $max_premium . "))";
                } else {
                    $dt .= " AND ((premium_rate >=" . $min_premium . " AND premium_rate <= " . $max_premium . ") OR (premium_with_tax >=" . $min_premium . " AND premium_with_tax <= " . $max_premium . "))  ";

                }
            } else {

                $dt .= " AND ((premium_rate >=" . $max_premium . ") OR (premium_rate >=" . $max_premium . "))";
            }
        }
        $rate = $this->apimodel->getdata1("master_policy_premium", "*", $dt);

        if ($partner_id) {
            $rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = $policy AND sum_insured = $sum_insured");

        }
        if (!empty($rate)) {
            if ($rate[0]->premium_rate == '') {
                $premium = $rate[0]->premium_with_tax;
            } else {
                $premium = $rate[0]->premium_rate;
            }
            if ($ptype == 1) {
                $r['amount'] = $premium;
            } else {
                $r['amount'] = ($sum_insured / 1000) * $premium;
            }
            if ($rate[0]->is_taxable) {
                $r['tax'] = $r['amount'] * $this->config->item('tax') / 100;
            }
        } else {
            $r = 0;
        }
        return $r;
    }

    function getpolicypremiumfamilyconstructage($proposal_details_id, $policy, $sum_insured, $adult, $child, $age, $whereArray = null, $min_premium = null, $max_premium = null, $partner_id = null)
    {

        //$ptype = $this->db->get_where("master_policy",array("master_policy_id"=> $policy))->row()->premium_type;

        //echo $policy.":::testing";
        $r = [];
        $ptype = $this->db->get_where("master_policy", array("policy_id" => $policy))->row()->premium_type;
//print_r($ptype);
        $dt = '';
        if ($ptype == 1) {
            $dt = "master_policy_id = $policy AND sum_insured = $sum_insured AND (adult_count = $adult AND child_count = $child)";
        } else {
            $dt .= "master_policy_id = $policy AND (adult_count = $adult AND child_count = $child) AND min_age <= $age AND max_age >= $age";

        }
        if ($whereArray == 1) {
            if ($min_premium != 0) {
                if ($min_premium == 3000) {
                    $dt .= " AND ((premium_rate <=" . $min_premium . ") OR (premium_rate <=" . $max_premium . "))";
                } else {
                    $dt .= " AND ((premium_rate >=" . $min_premium . " AND premium_rate <= " . $max_premium . ") OR (premium_with_tax >=" . $min_premium . " AND premium_with_tax <= " . $max_premium . "))  ";

                }
            } else {

                $dt .= " AND ((premium_rate >=" . $max_premium . ") OR (premium_rate >=" . $max_premium . "))";
            }
        }
        $rate = $this->apimodel->getdata1("master_policy_premium", "*", $dt);
        if ($partner_id) {
            $rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = $policy AND sum_insured = $sum_insured");

        }

        //print_r($rate[0]->policy_premium_id);
        if ($rate) {

            if ($ptype == 1) {
                $pamount = $rate[0]->premium_rate;
                $r['amount'] = $pamount;

                if ($rate[0]->premium_with_tax) {
                    //$r['tax'] = $pamount * $rate[0]->premium_with_tax/100;
                    $r['tax'] = $rate[0]->premium_with_tax;
                } else {
                    //$r['tax'] =  ($pamount  * 0.18) + $pamount;
                    $r['tax'] = $pamount * 0.18;
                }
            } else {
                $pamount = ($sum_insured / 1000) * $rate[0]->premium_rate;

                if ($rate[0]->premium_with_tax) {
                    $r['tax'] = $rate[0]->premium_with_tax;
                } else {
                    $r['tax'] = $pamount * 0.18;
                }
            }
        }
        if (empty($r)) {
            $r = 0;
        }


        # old code
        /*
        if(!empty($rate)) {
            $trate = $this->apimodel->getdata1("proposal_policy_member","premium"," policy_id = $proposal_details_id AND lead_id = $lead_id ORDER BY premium DESC");
            if(!empty($trate) && $trate[0]->premium > $pamount){
                $r['amount'] = $trate[0]->premium;
                if($rate[0]->is_taxable){
                    $r['tax'] = $trate[0]->premium*$this->config->item('tax')/100;
                }
            }else{
                $r['amount'] = $pamount;
                if($rate[0]->is_taxable){
                    $r['tax'] = $pamount*$this->config->item('tax')/100;
                }
            }
        }else{
            $r = 0;
        }
        ***/
        #
        return $r;
    } // EO ()


    function getpolicypremiummemberage($policy, $sum_insured, $age, $whereArray = null, $min_premium = null, $max_premium = null, $partner_id = null)
    {
        //echo json_encode(2213123);exit;
        $ptype = $this->db->get_where("master_policy", array("policy_id" => $policy))->row()->premium_type;
        // echo json_encode($ptype);exit;
        $dt = '';
        if ($ptype == 1) {
            $dt = "master_policy_id = $policy AND sum_insured = $sum_insured AND min_age <= $age AND max_age >= $age";
        } else {
            $dt .= "master_policy_id = $policy AND min_age <= $age AND max_age >= $age";

        }
        if ($whereArray == 1) {
            if ($min_premium != 0) {
                if ($min_premium == 3000) {
                    $dt .= " AND ((premium_rate <=" . $min_premium . ") OR (premium_rate <=" . $max_premium . "))";
                } else {
                    $dt .= " AND ((premium_rate >=" . $min_premium . " AND premium_rate <= " . $max_premium . ") OR (premium_with_tax >=" . $min_premium . " AND premium_with_tax <= " . $max_premium . "))  ";

                }
            } else {

                $dt .= " AND ((premium_rate >=" . $max_premium . ") OR (premium_rate >=" . $max_premium . "))";
            }
        }
        $rate = $this->apimodel->getdata1("master_policy_premium", "*", $dt);
        if ($partner_id) {
            $rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = $policy AND sum_insured = $sum_insured");

        }
        if ($rate) {
            if ($ptype == 1) {

                $r['amount'] = $rate[0]->premium_rate;
            } else {

                $r['amount'] = ($sum_insured / 1000) * $rate[0]->premium_rate;
            }
            //$this->config->item('tax') = 18;
            if ($rate[0]->is_taxable) {
                $r['tax'] = $rate[0]->premium_with_tax;
            }
        } else {
            $r = 0;
        }
        return $r;
    }

    function gettotalpremium($id, $sibasis, $lead_id, $policy_id)
    {

        $rates = $this->apimodel->getdata1("proposal_policy_member", "premium,tax", " proposal_policy_id = $id AND lead_id = $lead_id AND policy_id = $policy_id");
        $total['amount'] = 0;
        $total['tax'] = 0;
        if (!empty($rates)) {
            if ($sibasis == 1) {
                foreach ($rates as $rate) {
                    $total['amount'] = $total['amount'] + $rate->premium;
                    $total['tax'] = $total['tax'] + $rate->tax;
                }
            }
            if ($sibasis == 2) {
                foreach ($rates as $rate) {
                    $total['amount'] = $rate->premium;
                    $total['tax'] = $rate->tax;
                }
            }
            if ($sibasis == 3) {
                $count = count($rates);
                $value = 0;
                $tax = 0;
                foreach ($rates as $rate) {
                    if ($rate->premium > $value)
                        $value = $rate->premium;
                    $tax = $rate->tax;
                }
                $total['amount'] = $value;
                $total['tax'] = $tax;
            }
            if ($sibasis == 4) {
                foreach ($rates as $rate) {
                    $total['amount'] = $total['amount'] + $rate->premium;
                    $total['tax'] = $total['tax'] + $rate->tax;
                }
            }
        }
        return $total;
    }

    public function fetchPartnerDetails()
    {

        $data = $this->db->get_where("master_ceditors", ["creditor_id" => $_POST["partner_id"], "isactive" => 1])->row_array();
        if ($data) {
            $dataa = $this->db->get_where("master_single_journey", ["creditor_id" => $_POST["partner_id"], "is_active" => 1])->row_array();
            if (empty($dataa)) {
                $data = [];
            }
        }
        echo json_encode($data);
        exit;
    }

    public function get_suminsured_type_data()
    {
        $data = $this->db->get_where("master_suminsured_type", ["isactive" => 1])->result_array();
        echo json_encode($data);
        exit;
    }

    public function fetch_additional_plans()
    {
        $where = "pmpd.additional_plan_flag = 1 AND pmpd.customer_id = '" . $_POST['customer_id'] . "' AND pmpd.lead_id = '" . $_POST['lead_id'] . "'";
        $data = $this->db->select("mpst.policy_sub_type_name,mp.policy_id,mp.plan_id,mp.insurer_id,mp.policy_start_date,mp.policy_end_date,mpp.plan_name,mi.insurer_name, mpp.creditor_id, TIMESTAMPDIFF(YEAR, mp.policy_start_date, mp.policy_end_date) as duration, mc.creaditor_name, mc.creditor_logo")
            ->from('policy_member_plan_details pmpd')
            ->join('master_policy mp', 'mp.policy_id = pmpd.policy_id')
            ->join('master_policy_sub_type mpst', 'mpst.policy_sub_type_id = mp.policy_sub_type_id')
            ->join('master_plan mpp', 'mp.plan_id = mpp.plan_id AND mpp.isactive = 1')
            ->join('master_insurer mi', 'mi.insurer_id = mp.insurer_id AND mi.isactive = 1')
            ->join('master_ceditors mc', 'mc.creditor_id = mpp.creditor_id')
            ->where($where)
            ->get()->result_array();
        echo json_encode($data);
        exit;
    }
    public function getMember_insure_details_coi()
    {

        $lead_id = $this->input->post('lead_id');
        //$customer_id = $this->input->post('customer_id');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');

        $check_data = $this->db->query("SELECT * from proposal_policy_member_details as pd  where pd.lead_id = '$lead_id'")->result_array();
        if (!empty($check_data)) {
            //fetch selected plans
            $planDetails = $this->db->query("SELECT * FROM policy_member_plan_details where lead_id = '$lead_id'")->result_array();
            // var_dump($this->db->last_query());
//            exit;

            foreach ($planDetails as $val) {

                $members_data = $this->db->query("SELECT *
                    FROM proposal_policy_member_details AS pd
                    LEFT JOIN proposal_policy_member pm ON pd.member_id = pm.member_id
                    LEFT JOIN master_policy AS mp ON pm.policy_id = mp.policy_id 
                    LEFT JOIN master_policy_sub_type AS mpst ON mpst.policy_sub_type_id = mp.policy_sub_type_id
                    LEFT JOIN family_construct AS fc ON pd.relation_with_proposal = fc.id
                    WHERE pm.policy_id = '" . $val['policy_id'] . "' AND pm.lead_id = '$lead_id'")->result_array();


                foreach ($members_data as $key => $val_p) {
                    $arr[$val_p['code']]['policy_sub_type_name'] = $val_p['policy_sub_type_name'];
                    $arr[$val_p['code']]['member'][$key]['member_type'] = $val_p['member_type'];
                    $arr[$val_p['code']]['member'][$key]['policy_member_first_name'] = $val_p['policy_member_first_name'];
                    $arr[$val_p['code']]['member'][$key]['policy_member_last_name'] = $val_p['policy_member_last_name'];
                    $arr[$val_p['code']]['member'][$key]['policy_member_dob'] = $val_p['policy_member_dob'];
                    $arr[$val_p['code']]['member'][$key]['policy_member_gender'] = $val_p['policy_member_gender'];
                    $arr[$val_p['code']]['member'][$key]['cover'] = $val['cover'];
                    $arr[$val_p['code']]['member'][$key]['premium'] = $val['premium'];
                    //array_push($data,$arr);
                }
            }


            echo json_encode($arr);
            exit;

        }
    }
    public function getMember_insure_details()
    {
        $lead_id = $this->input->post('lead_id');
        //$customer_id = $this->input->post('customer_id');
        $lead_id = encrypt_decrypt_password($lead_id, 'D');

        $check_data = $this->db->query("SELECT * from proposal_policy_member_details as pd  where pd.lead_id = '$lead_id'")->result_array();
        if (!empty($check_data)) {
            //fetch selected plans
            $planDetails = $this->db->query("SELECT * FROM policy_member_plan_details where lead_id = '$lead_id'")->result_array();
            /* var_dump($this->db->last_query());
             exit;*/

            foreach ($planDetails as $val) {

                $members_data = $this->db->query("SELECT *
                    FROM proposal_policy_member_details AS pd
                    LEFT JOIN proposal_policy_member pm ON pd.member_id = pm.member_id
                    LEFT JOIN master_policy AS mp ON pm.policy_id = mp.policy_id 
                    LEFT JOIN master_policy_sub_type AS mpst ON mpst.policy_sub_type_id = mp.policy_sub_type_id
                    LEFT JOIN family_construct AS fc ON pd.relation_with_proposal = fc.id
                    WHERE pm.policy_id = '" . $val['policy_id'] . "' AND pm.lead_id = '$lead_id'")->result_array();

//echo $this->db->last_query();
                foreach ($members_data as $key => $val_p) {

                    $arr[$val_p['code']][$key]['member_type'] = $val_p['member_type'];
                    $arr[$val_p['code']][$key]['policy_member_first_name'] = $val_p['policy_member_first_name'];
                    $arr[$val_p['code']][$key]['policy_member_last_name'] = $val_p['policy_member_last_name'];
                    $arr[$val_p['code']][$key]['policy_member_dob'] = $val_p['policy_member_dob'];
                    $arr[$val_p['code']][$key]['policy_member_gender'] = $val_p['policy_member_gender'];
                    $arr[$val_p['code']][$key]['cover'] = $val['cover'];
                    $arr[$val_p['code']][$key]['premium'] = $val['premium'];
                    //array_push($data,$arr);
                }
            }
//exit;

            echo json_encode($arr);
            exit;

        }
    }



}