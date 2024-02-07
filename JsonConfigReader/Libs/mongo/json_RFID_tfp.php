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
$data_new = RFID_dictionary($data_new);

// file_put_contents("rfid_json_pre_mssql.json", $data_new);

/** decoding and read indentificator */
$decode = json_decode($data_new, true);
$matNumber = $decode["stIdentification"]["liMaterialNumber"];

/** MSSQL  */
require '../mssql/mssqlClass v1.0.php';
$mssql = new msssql;
$data_post = $mssql->inserRecord($matNumber, $data_new, "RFID");

/** MONGO DB */
$check = RFID_mongo_check_existing($matNumber);
if (empty($check)) {
    RFID_mongo_insert_record(json_decode($data_post, true));
} else {
    RFID_mongo_update_record($matNumber, json_decode($data_post, true));
}
