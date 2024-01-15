<?php

if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}


class Encryption_accounts_verify extends CI_Controller 
{

  public function __construct() {
    parent::__construct();
      // if(!$this->session->userdata('account_verify')){
      //   redirect('accounts_login');
      // }
  }

  public function test(){
    echo"Test";      
  }

  public function generate_checksumjson_data(){  
    $data=$this->session->userdata('account_verify');
    $this->load->telesales_template("decryption_view.php",$data);

  }

  public function verify_generate_checksum_decryptjson_data(){

    $val=$this->input->post('json_cust_data');

    
      $data['json_data'] = $this->formatData($val);

    $this->load->telesales_template("decryption_view.php",$data);

  }

  // public function encrypt(){
  //   $plaintext = "916010005190116";
  //   $cipher = "aes-128-gcm";
  //   $key = "lumGSVCXwk2A6fRjS9GM/kpecgnemZJKMlpqMnsPwC8=";
    
  //   if (in_array($cipher, openssl_get_cipher_methods()))
  //   {
     
  //       $ivlen = openssl_cipher_iv_length($cipher);
  //       $iv = openssl_random_pseudo_bytes($ivlen);
  //       $ciphertext = openssl_encrypt($plaintext, $cipher, $key, $options=0, $iv, $tag);
  //       //store $cipher, $iv, and $tag for decryption later
  //       $original_plaintext = openssl_decrypt($ciphertext, $cipher, $key, $options=0, $iv, $tag);
  //       echo $ciphertext."\n";
  //   }
  // }

  public function encrypt(){
    //$key previously generated safely, ie: openssl_random_pseudo_bytes
  $plaintext = "916010005190116";
  $key = "lumGSVCXwk2A6fRjS9GM/kpecgnemZJKMlpqMnsPwC8=";
  $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
  $iv = openssl_random_pseudo_bytes($ivlen);
  $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA);
  $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
  $ciphertext = base64_encode( $hmac.$ciphertext_raw );

  echo $ciphertext;exit;
  //decrypt later....
  $c = base64_decode($ciphertext);
  $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
  $iv = substr($c, 0, $ivlen);
  $hmac = substr($c, $ivlen, $sha2len=32);
  $ciphertext_raw = substr($c, $ivlen+$sha2len);
  $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
  $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
  if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
  {
      echo $original_plaintext."\n";
  }
  }

//function for decryption
  public function formatData($data){
    // $key = "GenericAPI_Axis_abhi@123456";
    $key = "lumGSVCXwk2A6fRjS9GM/kpecgnemZJKMlpqMnsPwC8=";
    $iv = 'encryptionIntVec';
    (27 == strlen($key)) or $key = hash('MD5', $key, true);
    (16 == strlen($iv)) or $iv = hash('MD5', $iv, true);
    $data = base64_decode($data);
    $data = openssl_decrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return $data;
  }

//function for encryption
  // public function encryptData($data){
  //   $key = "GenericAPI_Axis_abhi@123456";
  //   $iv = 'encryptionIntVec';
  //   (27 == strlen($key)) or $key = hash('MD5', $key, true);
  //   (16 == strlen($iv)) or $iv = hash('MD5', $iv, true);
  //   $encryptedData = base64_encode(openssl_encrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv));
  //   return $encryptedData;
  // }

  public function logout(){
    $this->session->unset_userdata('account_verify');
    redirect('accounts_login');

  }

}
