<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Generate_otp_abc extends CI_Controller 
{

    function __construct(){
        parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();

    }
    public function index(){
        echo"Working fine";
    }

    public function send_otp(){
        
        print_r($this->input->post());

    }

}