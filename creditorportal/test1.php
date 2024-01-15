<?php

// Server and login details
$host = 'healthrenewalnotices.blob.core.windows.net';
$username = 'healthrenewalnotices.nobrokers';
$password = 'BD//+EtWR6u2P4CeTHCzTpRgzPEq7nc5';
$port = 22;

// Path to remote file to download
//$remoteFile = '/path/to/remote/file.txt';

// Path to local file to save downloaded file
//$localFile = '/path/to/local/file.txt';
echo "start";
//die();
// Connect to SFTP server
echo $connection = ssh2_connect($host, 22);
if (!$connection) {
    die('Failed to connect to server');
}
echo $connection;
// Authenticate with server using username and password
if (!ssh2_auth_password($connection, $username, $password)) {
    die('Failed to authenticate with server');
}

// Open SFTP session
$sftp = ssh2_sftp($connection);

echo $sftp;
// Download remote file
//$remoteFileStream = ssh2_sftp_fopen($sftp, $remoteFile, 'r');
//$localFileStream = fopen($localFile, 'w');

//if (!$remoteFileStream || !$localFileStream) {
//    die('Failed to open file streams');
//}

//while (!feof($remoteFileStream)) {
//    fwrite($localFileStream, fread($remoteFileStream, 8192));
//}

//fclose($remoteFileStream);
//fclose($localFileStream);

echo 'File downloaded successfully!';

?>
