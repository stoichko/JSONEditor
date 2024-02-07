<?php

$servername = "172.18.1.198";
$username = "mubg_traysealer";
$password = "LBX59eyCWKLPEtVz";
$db = "die_database_tx_dev";
require '../vendor/autoload.php';

$client = new MongoDB\Client("mongodb://mubg_traysealer:LBX59eyCWKLPEtVz@172.18.1.198:27017");

$file = file_get_contents("jd_q7tDlqjI41.json");
$json_data = json_decode($file,true);

/** write data */

/* 0 = Dieset
1 = Top Section
2 = Bottom Section
3 = Tray Carrier
4 = Tray Gripper
5 = Tray Lifter
6 = няма, защото това е само general а не е merge
*/ 
$collection_name = "dieSets_test";
$collection = $client->$db->$collection_name;
$cursor = $collection->find(
    [
        
       "properties.serialnumber" => $json_data[0]["properties"]["serialnumber"],
       "properties.id" => 10
       
    ]
   
 );
 //$arr = (array)$cursor;
if (!$cursor->isDead()) {
    echo("I found something"); // функция за едит
}else echo("it's dead"); // финкция за създаване на нов запис. имам я по долу почти готова


 die();
//print_r($json_data[0]["properties"]["description"]);

/**Insert Dieset to MongoDB */

$collection_name = "dieSets_test";
$collection = $client->$db->$collection_name;
$document = $json_data[0];
//print_r($document);

$collection->insertOne($document);

// insert from Top section to Tray lifter

$collection_name = "dies_test";
$collection = $client->$db->$collection_name;
for ($i=1; $i <= 5; $i++){
    $document = $json_data[$i];
    $collection->insertOne($document);
    
}
