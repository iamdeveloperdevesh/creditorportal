<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PerMileRater extends Rater
{
    protected $age;

    protected $tenure;

    protected $number_of_ci = 0;

    public function getResults()
    {
        $this->age = $this->data['age'];
        $this->tenure = $this->data['tenure'];

        if ($this->isCIPolicy()) {
            $this->number_of_ci = $this->getNumberOfCI();
        }

        $result = $this->apimodel->getPerMileWisePremium(array_merge($this->getDefaultArguments(), ['number_of_ci' => $this->number_of_ci, 'age' => $this->age, "tenure" => $this->tenure]));

        return $result;
    }

    private function isCIPolicy()
    {
        return $this->policy_sub_type_id == 3;
    }

    private function getNumberOfCI()
    {
        return (int) $this->data['number_of_ci'] ?? 0;
    }
}
