<?php

if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}


class Encryption_accounts_verify extends CI_Controller 
{

  public function aesGcmEncrypt()
  {
      $plaintext = "916010005190116";//post plain text here

      $key = "lumGSVCXwk2A6fRjS9GM/kpecgnemZJKMlpqMnsPwC8=";
      $ivlen = openssl_cipher_iv_length($cipher = "aes-128-gcm");
      $iv = openssl_random_pseudo_bytes($ivlen);
      $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_NO_PADDING, $iv, $tag);
      $ciphertext = base64_encode($iv . $ciphertext_raw . $tag);
      return $ciphertext;
  }
  
  public function decrypt()
  {

      $str = "enctyptedvalue"; //post encrypted value here

      $key = "lumGSVCXwk2A6fRjS9GM/kpecgnemZJKMlpqMnsPwC8=";
      $encrypt = base64_decode($str);
      $ivlen = openssl_cipher_iv_length($cipher = "aes-128-gcm");
      $tag_length = 16;
      $iv = substr($encrypt, 0, $ivlen);
      $tag = substr($encrypt, -$tag_length);
      $ciphertext = substr($encrypt, $ivlen, -$tag_length);
  
      $ciphertext_raw = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_NO_PADDING, $iv, $tag);
      return $ciphertext_raw;
  }

}
