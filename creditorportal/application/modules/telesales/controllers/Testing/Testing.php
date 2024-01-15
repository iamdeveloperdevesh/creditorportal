<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once(APPPATH . "controllers/MY_TelesalesSessionCheck.php");

class Testing extends MY_TelesalesSessionCheck
{
    
    
   
    public function __construct()
    {
        
        parent::__construct();

       
    }


    
    public function index()
    {
        $this->load->telesales_template("testing/do_home.php"); 
    }




   



}
