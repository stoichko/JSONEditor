<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
date_default_timezone_set('Europe/Berlin');
require "mongo_read_insert_update.php";

/** reading raw information form the form */
$data = file_get_contents('php://input');

/** JSON formating */
$data_new = json_formating($data);
$data_new = machine_dictionary($data_new);

/** decoding and read indentificator */
$decode = json_decode($data_new, true);
$SerialNumber = $decode["stIdentification"]["liSerialNumber"];

/** MSSQL  */
require '../mssql/mssqlClass v1.0.php';
$mssql = new msssql;
$data_post = $mssql->inserRecord($SerialNumber, $data_new, "Machine");

/** MONGO DB */
$check = machine_check_exsisting($SerialNumber);
if (empty($check)) {
    machine_mongo_insert_record(json_decode($data_post, true));
} else {
    machine_mongo_update_record($SerialNumber, json_decode($data_post, true));
}
