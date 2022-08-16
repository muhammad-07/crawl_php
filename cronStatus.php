<?php

// $dbhost = 'localhost';
// $dbuser = 'root';
// $dbpass = 'asd12D';
// $dbname = 'scrap';

$dbhost = 'localhost';
$dbuser = 'ycgoc9gp_bot';
$dbpass = 'rav21@21@21';
$dbname = 'ycgoc9gp_bot';


$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if ($mysqli->connect_errno) {
  printf("Connect failed: %s<br />", $mysqli->connect_error);
  exit();
}


$sql = "SELECT * FROM cron ORDER BY cron_id DESC LIMIT 1 ";

$result = $mysqli->query($sql);
$lastCrawled = 0;
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $lastCrawled = $row["crowled_till"];
  }
}
// else {
//   printf('No record found.<br />');
// }
mysqli_free_result($result);

$sql = "SELECT * FROM tbl_contractAddress";

$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    $total = $result->num_rows;
}
mysqli_free_result($result);

$sql = "SELECT * FROM tbl_user_data";

$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    $crowled = $result->num_rows;
}
mysqli_free_result($result);

echo "Cron job is <span class='badge badge-success'>working</span> and currently at ID ".$lastCrawled. " total records crawled ".$crowled." out of ".$total;