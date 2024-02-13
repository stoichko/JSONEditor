<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
date_default_timezone_set('Europe/Berlin');
require "DSU_RFID_get_info.php";

$data = file_get_contents('php://input');
file_put_contents("basic_dieset_json_raw.json", $data);

// $data = file_get_contents("basic_dieset_json_raw.json");
// die();




function dictionary($data)
{
    $data_decode = json_decode($data, true);
    $aliProjektNumbers = $data_decode["aliProjektNumbers"]; // + втория лист, ако има нещо в него
    creatJSON($aliProjektNumbers);
    $stHeader = array(
        'uiStructureVersion' => $data_decode['uiStructureVersion'],
        'uiDataVersion' => $data_decode['uiDataVersion'],
        'szCreator' => $data_decode['szCreator'],
        'dtCreated' => $data_decode['dtCreated'],
        'szState' => $data_decode['szState'],
    );
    $stIdentification = array(

        'liMachineSerialNumber' => $data_decode['liMachineSerialNumber'],
        'aliProjektNumbers' => array_map('intval', explode(', ', $data_decode['aliProjektNumbers'])),
        'szMachineType' => $data_decode['szMachineType'],
        'szDiesetDescription' => (empty($data_decode['szDiesetDescription']) ? "" : $data_decode['szDiesetDescription']),
    );

    $stFooter = array(
        'bEndOfFile' => $data_decode['bEndOfFile'] == "true" ? true : false,
    );

    $result = array(
        'stHeader' => $stHeader,
        'stIdentification' => $stIdentification,
        'stDies' => creatJSON($aliProjektNumbers),
        'stFooter' => $stFooter,
    );


    return $result;
}
$JSON_basic_dieset = dictionary($data);
$matNumber = (int)$JSON_basic_dieset['stIdentification']['liMachineSerialNumber'];
// var_dump($matNumber);

// echo '<pre>';
// print_r(json_encode($JSON_basic_dieset, JSON_PRETTY_PRINT));
// die();
// echo '</pre>';
/** MSSQL  */
require '../../mssql/mssqlClass v1.0.php';
$mssql = new msssql;
$data_post = $mssql->inserRecord($matNumber, json_encode($JSON_basic_dieset), "Basic");
