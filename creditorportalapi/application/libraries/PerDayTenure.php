<?php

class PerDayTenure extends Rater
{
    protected $tenure;

    public function getResults()
    {
        $this->tenure = $this->data['tenure'];
        $result = $this->apimodel->getPolicyPerDayTenurePremium(array_merge($this->getDefaultArguments(), ['tenure' => $this->tenure]));
        return $result;
    }
}
