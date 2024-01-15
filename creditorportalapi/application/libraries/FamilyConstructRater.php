<?php

defined('BASEPATH') or exit('No direct script access allowed');

class FamilyConstructRater extends Rater
{
    protected $adult_count;
    protected $child_count;


    public function getResults()
    {
        $this->adult_count = $this->data['adults_to_calculate'];
        $this->child_count = $this->data['children_to_calculate'];

        $result = $this->apimodel->getPolicyPremiumFamilyConstruct(array_merge($this->getDefaultArguments(), ['adult_count' => $this->adult_count, "child_count" => $this->child_count]));

        return $result;
    }
}
