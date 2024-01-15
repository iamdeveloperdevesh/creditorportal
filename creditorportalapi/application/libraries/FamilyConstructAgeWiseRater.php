<?php

defined('BASEPATH') or exit('No direct script access allowed');

class FamilyConstructAgeWiseRater extends Rater
{
    protected $age;

    protected $adult_count;

    protected $child_count;

    public function getResults()
    {
        $this->age = $this->data['age'];
        $this->adult_count = $this->data['adults_to_calculate'];
        $this->child_count = $this->data['children_to_calculate'];

        $result = $this->apimodel->getFamilyConstructAgeWisePremium(array_merge($this->getDefaultArguments(), ['adult_count' => $this->adult_count, 'age' => $this->age, "child_count" => $this->child_count]));
        
        return $result;
    }
}
