<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
date_default_timezone_set('Europe/Berlin');
// require "mongo_communication.php";
require "mongo_read_insert_update.php";

// $sn = (int)$_GET['serial'];
// $id = (int)$_GET['id'];
$SerialNumber = (int)$_GET['liSerialNumber'];


$read = machine_mongo_read($SerialNumber);


// print_r(json_encode($read));
//replace tabs with two spaces
//10.04.2023
$json_indented_by_4 = json_encode($read, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
echo '<pre>' . preg_replace('/^(  +?)\\1(?=[^ ])/m', '$1', $json_indented_by_4) . '</pre>';

// print_r(($MaterialNumber));