<?php

require 'mssqlClass v1.0.php';

$mssql = new msssql;

// print_r(
//     $mssql->readRecord(100000000)
// );
echo '<pre>';
$json = file_get_contents('testDSU.json');

$mssql->inserRecord(999999999, $json, "DSU");
