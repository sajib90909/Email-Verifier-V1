<?php
include 'pagename.php';
session_start();
if (!empty($_GET['token']) && $_GET['token'] == $_SESSION['key']) { //check token value for csrf protection;
    include '../config/'.$config;
    include "../config/".$database;
    $db = new database();
    function test_input($data) {
        $db = new database();
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $data = strtolower($data);
        $data = mysqli_real_escape_string($db->link, $data);
        return $data;
    }
    $start_from = test_input($_GET['start']);
    $limit = test_input($_GET['limit']);
    $validation = test_input($_GET['validation']);
    if ($validation != 'selected') { // select data considering limit and velidation;
        if ($validation == 'valid') {
            $query22 = "SELECT id,name,company_name,email,validation FROM data WHERE validation  = 'Valid' LIMIT $start_from,$limit";
        } else {
            $query22 = "SELECT id,name,company_name,email,validation FROM data WHERE validation  != 'Valid' LIMIT $start_from,$limit";
        }
    } else {
        $query22 = "SELECT id,name,company_name,email,validation FROM data LIMIT $start_from,$limit";
    }
    $read22 = $db->select($query22);
    if ($read22) {
        $fn = "csv_" . uniqid() . ".csv"; //make a uniq id for csv file;
        header('Content-type:text/csv;charset=utf-8'); //declear content-type;
        header('Content-Disposition: attachment; filename=' . $fn); //assign file name to csv file;
        echo "\xEF\xBB\xBF"; // BOM header UTF-8
        $file = fopen("php://output", "w"); //write to csv file;
        fputcsv($file, array('ID', 'Name', 'Company Name', 'Email', 'Validation')); //headers name
        while ($row22 = $read22->fetch_assoc()) { //write data;
            fputcsv($file, $row22);
        }
        fclose($file); //close file;

    }
} else {
    header("Location: ../".$index);
}
?>
