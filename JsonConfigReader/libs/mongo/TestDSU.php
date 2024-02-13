<?php

$data = file_get_contents('php://input');
file_put_contents('TestDSUraw.json', $data);

$arr = json_decode($data, true);
$liMaterialNumber = $arr['liMaterialNumber'];
$filename = 'TestDSUcache/TestDSUraw_' . $liMaterialNumber;
file_put_contents("$filename.json", $data);
