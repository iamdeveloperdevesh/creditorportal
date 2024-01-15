<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Justlocal extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        //var_dump($_SESSION);
    }

    public function index()
    {
		
        $this->load->view('justlocal/index.html');

       
        
    }
	public function benefithub()
    {
		//echo 1;exit;
        $this->load->view('justlocal/benfithub.html');

       
        
    }
	
	
}	
