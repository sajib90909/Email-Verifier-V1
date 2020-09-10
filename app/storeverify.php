<?php
include 'pagename.php';
session_start();
extract($_POST);
if(!empty($_POST['token']) && $_POST['token'] == $_SESSION['key']){
  include '../config/'.$config;
  include "../config/".$database;
$db = new database();
//filter all value
function test_input($data) {
$db = new database();
$data = trim($data);
$data = stripslashes($data);
$data = htmlspecialchars($data);
$data = strtolower($data);
$data = mysqli_real_escape_string($db->link,$data);
return $data;
}
$name = test_input($name);
$companyname = test_input($companyname);
$email = test_input($email);
$validation = test_input($validation);
$status = test_input($status);
if(!empty($email)){
  $search_q = "SELECT * FROM data WHERE email = '$email' ";
  $search_r = $db->select($search_q);
  if($search_r){
    $count = mysqli_num_rows($search_r);
    if ($count > 0) {
      $search_q = "SELECT * FROM data WHERE email = '$email' AND status = '$status' ";
      $search_r = $db->select($search_q);
      if($search_r){
        $count = mysqli_num_rows($search_r);
        if ($count > 0) {

        }else{
          $query18 = "UPDATE data SET status = '$status', validation = '$validation' WHERE email = '$email' " ;
          $read18 = $db->update($query18);
          if ($read18) {
          }
        }
    }
    }else{
        $query = "INSERT INTO data ( name, company_name, 	email, validation, status)
        VALUES ('$name','$companyname','$email','$validation','$status')";
        $read = $db->insert($query);
        if ($read) {
        }
      }
  }
}
}else{
  header("Location: ../".$index);
}
?>
