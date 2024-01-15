<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MemberAgeWiseRater extends Rater
{
    protected $age;

    public function getResults()
    {
        $this->age = $this->data['age'];

        $result = $this->apimodel->getPolicyMemberAgeWisePremium(array_merge($this->getDefaultArguments(), ['age' => $this->age]));

        return $result;
    }
}
