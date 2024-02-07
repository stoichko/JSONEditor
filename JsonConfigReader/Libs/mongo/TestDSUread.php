<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
date_default_timezone_set('Europe/Berlin');

$liMaterialNumber = (int)$_GET['liMaterialNumber'];

$data = file_get_contents('TestDSUcache/TestDSUraw_' . $liMaterialNumber . '.json');
print_r($data);
