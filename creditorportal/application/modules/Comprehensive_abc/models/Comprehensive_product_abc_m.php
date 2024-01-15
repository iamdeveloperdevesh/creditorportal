<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comprehensive_product_abc_m  extends CI_Model
{

    function __construct(){
        parent::__construct();
        $this->emp_id=1;
    }

    function product_display_abc()
    {
       /* $result = $this
        ->db
        ->query
        ("SELECT t.*, SUM(t.premium) AS abc_premium FROM (SELECT e.policy_detail_id , e.product_name , e.product_image , e.product_desc , e.is_parent , e.is_combo AS 
        is_combo ,e.product_type , MIN(CAST(f.premium AS UNSIGNED)) as premium FROM family_construct_age_wise_si f inner join employee_policy_detail e  
        on e.policy_detail_id = f.policy_detail_id GROUP BY f.policy_detail_id HAVING e.product_type = 1) AS t GROUP BY t.is_combo;")
                ->result_array();*/ 
        //         $result = $this
        //         ->db
        //         ->query
        //         ("SELECT t.*, SUM(t.premium) AS abc_premium FROM (SELECT e.policy_detail_id , e.product_name , e.product_image , e.product_desc , e.is_parent,e.sequence AS sequence , e.is_combo AS 
        // is_combo ,e.product_type , MIN(CAST(f.premium AS UNSIGNED)) as premium,e.brochure_doc_name as brochure_doc_name, e.faq_doc_name as faq_doc_name FROM family_construct_age_wise_si f inner join employee_policy_detail e  
        // on e.policy_detail_id = f.policy_detail_id GROUP BY f.policy_detail_id HAVING e.product_type = 1) AS t GROUP BY t.is_combo ORDER BY t.sequence ASC;")
        //         ->result_array();


        $this->db->select('epd.*,mpst.main_product_name');
        $this->db->from('employee_policy_detail as epd');
        $this->db->join('product_master_with_subtype as mpst', 'epd.product_name = mpst.product_name');
        $this->db->where(['epd.product_type'=>'1','epd.is_parent'=>'0']);
        $this->db->where(['mpst.main_product_name'=>$this->product_id]);
        $this->db->order_by("epd.sequence", "desc");
        $parentProducts = $this->db->get()->result_array();
        foreach ($parentProducts as $productKey => $product) {
            $comboPolicies = $this->Abc_m->getComboPolicy($product['policy_detail_id']);
            if(empty($comboPolicies)){
                $result = $this
                        ->db
                        ->query
                        ("SELECT SUM(t.premium) AS abc_premium, t.sum_insured_word AS abc_sum_insured FROM (SELECT e.policy_detail_id , e.product_name , e.product_image , e.product_desc , e.is_parent,e.sequence AS sequence , e.is_combo AS 
                is_combo ,e.product_type , MIN(CAST(f.premium AS UNSIGNED)) as premium,f.sum_insured_word,e.brochure_doc_name as brochure_doc_name, e.faq_doc_name as faq_doc_name FROM family_construct_age_wise_si f inner join employee_policy_detail e  
                on e.policy_detail_id = f.policy_detail_id GROUP BY f.policy_detail_id HAVING e.product_type = 1 AND e.policy_detail_id = ".$product['policy_detail_id'].") AS t GROUP BY t.is_combo ORDER BY t.sequence ASC;")
                        ->row_array();
                $parentProducts[$productKey]['abc_premium'] = $result['abc_premium'];
                $parentProducts[$productKey]['abc_default_si'] = $result['abc_sum_insured'];
                $parentProducts[$productKey]['default_construct'] = 'Self';
            } else {
                $defaultSumInsureOptionQuery = "SELECT pcd.id,pc.parent_policy_detail_id,pc.parent_sum_insured,mpst.policy_sub_type_short, GROUP_CONCAT(CONCAT_WS(' ', mpst.policy_sub_type_short, pcd.sum_insured_words) SEPARATOR ' - ') AS si_options
                    FROM combo_policy_suminsured_cobmination AS pc, combo_policy_suminsured_details AS pcd, master_policy_sub_type AS mpst
                    WHERE pc.is_default = 1 AND pc.id = pcd.option_number AND pc.parent_policy_detail_id = '".$product['policy_detail_id']."' AND pcd.policy_subtype_id = mpst.policy_sub_type_id
                    GROUP BY pcd.option_number";
                $defaultSumInsureOption = $this->db->query($defaultSumInsureOptionQuery)->row_array();

                $premium = 0;
                array_push($comboPolicies,$product);
                $minPremium = 0;
                $minConstruct = '1A';
                foreach ($comboPolicies as $key => $comboPolicy) {
                    $qry = "SELECT cpd.policy_detail_id,cpd.sum_insured_amount,cpd.sum_insured_words FROM combo_policy_suminsured_cobmination AS cp INNER JOIN combo_policy_suminsured_details AS cpd ON cp.id = cpd.option_number WHERE cp.is_default = 1 AND cpd.policy_detail_id = ".$comboPolicy['policy_detail_id'];
                    $defaultSumInsure = $this->db->query($qry)->row_array();
                    $arrayToPass = [
                        'suminsured_type'=>$comboPolicy['suminsured_type'],
                        'family_construct'=>'1A',
                        'sum_insure'=>$defaultSumInsure['sum_insured_words'],
                        'policy_id'=>$comboPolicy['policy_detail_id'],
                        'max_age'=>25
                    ];
                    $premium += $this->Abc_m->getPremium($arrayToPass);
                }
                $parentProducts[$productKey]['abc_premium'] = $premium;
                $parentProducts[$productKey]['abc_default_si'] = $defaultSumInsureOption['si_options'];
                $parentProducts[$productKey]['default_construct'] = 'Self';
            }
        }

        // $result = $this
        // ->db
        // ->query
        // ("SELECT output.*,SUM(output.fc_premium) AS sum_fc_premium,SUM(output.pc_premium) AS sum_pc_premium from (SELECT epd.*, fc.family_type, fc.age_group, MIN(CAST(fc.premium AS UNSIGNED)) AS fc_premium, MIN(CAST(pc.premium AS UNSIGNED)) AS pc_premium FROM employee_policy_detail AS epd LEFT JOIN family_construct_age_wise_si AS fc ON epd.policy_detail_id = fc.policy_detail_id LEFT JOIN policy_creation_age AS pc ON epd.policy_detail_id = pc.policy_id GROUP BY epd.policy_detail_id) AS output GROUP BY output.is_combo HAVING output.product_type = 1 ORDER BY output.sequence ASC;")
        // ->result_array(); 
        return $parentProducts;
    }

}
?>