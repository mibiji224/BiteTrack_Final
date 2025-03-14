<?php 	

$server_name = "sql105.infinityfree.com";
$username = "sql105.infinityfree.com";
$password = "Zaizen7891";
$dbname = "if0_38488649_nutrition_tracker";

// db connection
$connect = new mysqli($server_name, $username, $password, $dbname);
// check connection
if($connect->connect_error) {
  die("Connection Failed : " . $connect->connect_error);
} else {
  // echo "Successfully connected";
}

?>