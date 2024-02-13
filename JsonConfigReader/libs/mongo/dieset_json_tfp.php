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
$data_new = dieset_json_asz_combine($data_new);
$data_new = dieset_dictionary($data_new);

/** decoding and read indentificator */
$decode = json_decode($data_new, true);
$diesetNumber = $decode["stIdentification"]["liDiesetNumber"];

/** MSSQL  */
require '../mssql/mssqlClass v1.0.php';
$mssql = new msssql;
$data_post = $mssql->inserRecord($diesetNumber, $data_new, "Dieset");

/** MONGO DB */
$check = dieset_check_exsisting($diesetNumber);
if (empty($check)) {
    dieset_mongo_insert_record(json_decode($data_post, true));
} else {
    dieset_mongo_update_record($diesetNumber, json_decode($data_post, true));
}
