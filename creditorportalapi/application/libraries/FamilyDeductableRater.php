<?php
defined('BASEPATH') or exit('No direct script access allowed');

class FamilyDeductableRater extends Rater
{
    protected $adult_count;

    protected $child_count;

    protected $deductable;

    public function getResults()
    {
       // print_r($this->data);
        $this->adult_count = $this->data['adults_to_calculate'];
        $this->child_count = $this->data['children_to_calculate'];
        $this->deductable_si = $this->data['deductable_si'];
        $this->planwise_premium_api = $this->data['planwise_premium_api'];
        if($this->data['deductable'] == '1')
        {
            $this->deductable = 'No';
        }
        else{
            $this->deductable = $this->data['deductable'];
        }

        $this->age = $this->data['age'];

        $result = $this->apimodel->getPolicyFamilyDeductable(array_merge($this->getDefaultArguments(), ['adult_count' => $this->adult_count,  "child_count" => $this->child_count, 'deductable' => $this->deductable, 'age' => $this->age,'deductable_si'=> $this->deductable_si,'planwise_premium_api'=> $this->planwise_premium_api]));
       // print_r($result);
        return $result;
    }
}
