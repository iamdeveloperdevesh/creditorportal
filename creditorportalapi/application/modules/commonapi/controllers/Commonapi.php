<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use \Firebase\JWT\JWT;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

// session_start(); //we need to call PHP's session object to access it through CI
class Commonapi extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Commonapimodel', '', TRUE);
		// Load these helper to create JWT tokens
		$this->load->helper(['core_helper', 'jwt', 'authorization_helper']);

		//$this->load->helper(['jwt', 'authorization']);

		ini_set('memory_limit', '25M');
		ini_set('upload_max_filesize', '25M');
		ini_set('post_max_size', '25M');
		ini_set('max_input_time', 3600);
		ini_set('max_execution_time', 3600);
		ini_set('memory_limit', '-1');
		allowCrossOrgin();
	}

	function getallheaders_new()
	{
		return $response_headers = getallheaders_values();
	}

	//For generating random string
	private function generateRandomString($length = 8, $charset = "")
	{
		if ($charset == 'N') {
			$characters = '0123456789';
		} else {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	private function verify_request($token)
	{
		// Get all the headers
		//$headers = $this->input->request_headers();
		// Extract the token
		//$token = $headers['Authorization'];
		// Use try-catch
		// JWT library throws exception if the token is not valid
		try {
			// Validate the token
			// Successfull validation will return the decoded user data else returns false
			$data = AUTHORIZATION::validateToken($token);
			if ($data === false) {
				return json_encode(array("status_code" => "401", "Metadata" => array("Message" => "Unauthorized Access!"), "Data" => NULL));
				exit;
			} else {
				return $data;
			}
		} catch (Exception $e) {

			// Token is invalid
			// Send the unathorized access message
			
			return json_encode(array("status_code" => "401", "Metadata" => array("Message" => "Unauthorized Access!"), "Data" => NULL));
			exit;
		}
	}

	function checkUWCaseTest(){
		var_dump(checkUWCase(138,11,406));
	}

	//Test Api
	function testApi()
	{
		$send_message = send_message("8149212749", $mail_to = "", $mail_cc = "", $mail_bcc = "", $data = array(), "sendOTP");

		echo "<pre>";
		print_r($send_message);
		exit;
	}
}
