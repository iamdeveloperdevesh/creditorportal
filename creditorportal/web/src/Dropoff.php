<?php
error_reporting(E_ALL);
require_once('Dropoff.php');
function send_dropoff_mail_sms($msg,$delete,$emp_id){
	
	
	
		$conn22 = new mysqli('localhost', 'root', 'Cg04#OoI8H7O@X', 'axis_retail');
		//var_dump($conn22);
// Check connection
if ($conn22->connect_error) {
  die("Connection failed: " . $conn22->connect_error);
}


/* on message */
if($delete){
$sql1 = "SELECT * FROM temp_dropoff where identity = ".$emp_id;
$result1 = $conn22->query($sql1);
if ($result1->num_rows > 0) {
	$sql2 = "DELETE FROM temp_dropoff where identity = ".$emp_id;
	$result2 = $conn22->query($sql2);
}
$querycheckproductcode = "SELECT product_id FROM employee_details where emp_id = ".$emp_id;
$resultproductcode = $conn22->query($querycheckproductcode);
$type = '';
if ($resultproductcode->num_rows > 0) {
	$type = $resultproductcode->fetch_assoc()['product_id'];
	
}
$sql3 = "INSERT INTO temp_dropoff(empid,sendemail,identity,type) VALUES (".$msg.",'N',".$emp_id.",'".$type."')";
//echo $sql3;
$conn22->query($sql3);
/*on message ends */
}else{
	/*on close */
	$sql4 = "update  temp_dropoff set sendemail = 'Y' where empid =".$msg;
	echo $sql4;
	$conn22->query($sql4);
	/*on close ends*/
}

$conn22->close();
	
	if(!empty($emp_id)){
		$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://eb.benefitz.in/close_browser_dropoff_action?emp_id=".$emp_id,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/x-www-form-urlencoded",
    "Postman-Token: 1babbe99-1783-43f3-89e8-a647a720a164",
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
	
	}
}
	

?>

