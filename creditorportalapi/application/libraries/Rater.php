<?php

defined('BASEPATH') or exit('No direct script access allowed');

abstract class Rater
{
    protected $data;

    protected $is_individual_cover = false;

    protected $policy_id;

    protected $sum_insured;

    protected $policy_sub_type_code;

    protected $policy_sub_type_id;

    protected $member_type;

    protected $ci;

    protected $apimodel;

    protected $premium;

    protected $premium_without_tax;

    protected $messages;

    protected $group_code;

    protected $hospi_cash_group_code;

    protected $group_code_type;

    public function __construct($data)
    {
        $this->ci = $this->ci = &get_instance();
        $this->ci->load->model('apimodel', '', TRUE);
        $this->apimodel = $this->ci->apimodel;
        $this->data = $data;
        $this->is_individual_cover = $data['is_individual_cover'];
        $this->policy_id = $data['policy_id'];
        $this->sum_insured = $data['sum_insured'];
        $this->hospi_cash_group_code = $data['hospi_cash_group_code'];
        $this->group_code_type = $data['group_code_type'];
        $this->policy_sub_type_code = $data['policy_sub_type_code'];
        $this->policy_sub_type_id = $data['policy_sub_type_id'];
        $this->member_type = $data['member_type'];
        $result = $this->getResults();
        $this->messages = $result['messages'];
        $this->setPremiumProperties($result);
        $this->setGroupCode($result);
        $this->setGroupCodeType($result);
    }

    public abstract function getResults();

    public function getPremium()
    {
        return $this->premium;
    }

    public function getPremiumWithoutTax()
    {
        return $this->premium_without_tax;
    }

    public static function make($basis_id, $data)
    {
        $mapping = [
            1 => FlatRater::class,
            2 => FamilyConstructRater::class,
            3 => FamilyConstructAgeWiseRater::class,
            4 => MemberAgeWiseRater::class,
            5 => PerMileRater::class,
            6 => FamilyDeductableRater::class,
            7 => PerDayTenure::class
        ];

        return new $mapping[$basis_id]($data);
    }

    public function getPlanName()
    {
        if ($this->is_individual_cover) {
            return $this->policy_sub_type_code . "-" . $this->member_type;
        }

        return $this->policy_sub_type_code;
    }

    public function hasPremium()
    {
        return $this->premium != null;
    }

    protected function getDefaultArguments()
    {
        return [
            'policy_id' => $this->policy_id,
            'sum_insured' => $this->sum_insured,
            'hospi_cash_group_code' => $this->hospi_cash_group_code,
            'policy_sub_type_id' => $this->policy_sub_type_id,
            'group_code_type' => $this->group_code_type
        ];
    }

    protected function setPremiumProperties($result)
    {
        if (!$result['status']) {
            return;
        }
        $this->premium = $result['rate'];
        $this->premium_without_tax = $result['rate_without_tax'];
    }

    public function getMessages()
    {
        return $this->messages;
    }

    protected function setGroupCode($result)
    {
        if($this->member_type == 'Spouse'){
            
            $this->group_code = $result['group_code_spouse'] ?? '';
            return;
        }
        
        $this->group_code = $result['group_code'] ?? '';
    }

    protected function setGroupCodeType()
    {
        if($this->member_type == 'Spouse'){
            
            $this->group_code_type = 'group_code_spouse';
            return;
        }
            
        $this->group_code_type = 'group_code';
    }

    public function getGroupCode()
    {
        return $this->group_code;
    }

    public function getGroupCodeType()
    {
        return $this->group_code_type;
    }
}
