<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class TestingController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }
    public function curl()
    {

        $key = "XGaHm4";
        $salt = "dC2qLagI";
        $wsUrl = "https://info.payu.in/merchant/postservice?form=2";
        //$key = "nAtwzQ";
        //$salt = "TqhIAHgl";
        //$wsUrl = "https://test.payu.in/merchant/postservice.php?form=2";
        $command = "verify_payment";
        $var1 = "AXFP1653375131475304306"; // SourceTxnId

        $hash_str = $key  . '|' . $command . '|' . $var1 . '|' . $salt;
        $hash = strtolower(hash('sha512', $hash_str));

        $r = array('key' => $key, 'hash' => $hash, 'var1' => $var1, 'command' => $command);
        $qs = http_build_query($r);
        // print_r($qs);exit;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $wsUrl,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            // CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_ENCODING => '',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $qs,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ));

        $response = curl_exec($curl);
        
        $errors = curl_error($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        print_r(["info" => $info, "data" => $response, "errors" => $errors]);
    }
}
