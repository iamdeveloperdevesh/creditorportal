<?php
//require __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//require  $_SERVER["DOCUMENT_ROOT"].'/../vendor/autoload.php';
require_once '/var/www/html/fyntune-creditor-portal/vendor/autoload.php';

use phpseclib3\Net\SFTP;

$sftp = new SFTP('healthrenewalnotices.blob.core.windows.net');
$sftp->login('healthrenewalnotices.nobrokers', 'BD//+EtWR6u2P4CeTHCzTpRgzPEq7nc5');

$sftp->get('test1.php');


echo "file uploaded successfully1";
?>

