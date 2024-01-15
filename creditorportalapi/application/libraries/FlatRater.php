<?php
defined('BASEPATH') or exit('No direct script access allowed');

class FlatRater extends Rater
{
    public function getResults()
    {
        if($this->data['sum_insured'] == '')
        {
            $this->sum_insured = 'No';

        }
        $this->planwise_premium_api = $this->data['planwise_premium_api'];
       // print_r($this->planwise_premium_api);
        $result = $this->apimodel->getPolicyPremiumFlat(array_merge($this->getDefaultArguments(),['sum_insured' => $this->sum_insured,'planwise_premium_api'=> $this->planwise_premium_api]));
        return $result;
    }
}
