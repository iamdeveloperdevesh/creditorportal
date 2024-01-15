<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Telehome extends CI_Controller
{
    function __construct()
    {
      //  echo 23;die;
        parent::__construct();

    }

    function index()
    {
        $this->load->view('template/header_tele.php');
        $this->load->view('telehome/index');
        $this->load->view('template/footer_tele.php');
    }

    function logout()
    {
        if(!empty($_SESSION['telesales_session'])){
            unset($_SESSION['telesales_session']);
        }
        redirect("login");
    }
}

?>