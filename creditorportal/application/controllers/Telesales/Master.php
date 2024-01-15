<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once(APPPATH."controllers/MY_TelesalesSessionCheck.php");




class Master extends MY_TelesalesSessionCheck
{

    public function __construct()
    {
        parent::__construct();

        //var_dump($_SESSION);
    }

    public function index()
    {
	}
	
	
	

    public function create_lead()
    {
	
		//print_pre($_SESSION);exit;
		
        extract($this->input->post());
			$this->load->telesales_template("dummy.html");
    }

   

    
    
}
